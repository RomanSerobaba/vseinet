'use strict';

const extend = require('extend');

class Store {
    constructor(mysql) {
        this.mysql = mysql;
    }

    setRequestData(source, data) {
        const insert = `
            INSERT IGNORE INTO data_product (
                id, 
                base_product_id, 
                source_id, 
                name, 
                code, 
                ccode, 
                artikul, 
                url
            ) VALUES `;
        const placeholders = `(
                :id$n, 
                :base_product_id$n, 
                :source_id$n, 
                :name$n, 
                :code$n, 
                :ccode$n, 
                :artikul$n, 
                :url$n
            )`;
        const values = [];
        const parameters = {};
        data.map((row, index) => {
            for (let key in row) {
                parameters[key + index] = row[key];
            }
            values.push(placeholders.replace(/\$n/g, index));
        });
        return this.mysql.query(insert + values.join(', '), parameters);
    }

    getRequestData(source) {
        return this.mysql.queryRow(`
            SELECT 
                id, 
                base_product_id, 
                source_id, 
                name, 
                code, 
                ccode, 
                artikul, 
                url, 
                next_url, 
                data 
            FROM data_product 
            WHERE source_id = :source_id AND 
                (pending IS NULL OR (complete IS NULL AND pending < DATE_SUB(NOW(), INTERVAL 10 MINUTE))) 
            ORDER BY IF(next_url, 1, 2), IF(pending IS NULL, 1, 2) 
            LIMIT 1`,
            { source_id: source.id }
        )
        .then(row => { 
            if (row) {
                return this.mysql.query(`
                    UPDATE data_product 
                    SET pending = NOW() 
                    WHERE id = :id`, 
                    row
                )
                .then(() => row);
            }
            return null;
        });     
    }

    setResultData(source, data, result) {
        if (result.data) {
            data.data = data.data ? JSON.parse(data.data) : {};
            extend(true, data.data, result.data);
            data.data = JSON.stringify(data.data);
        }
        if (result.next_url) {
            return this.mysql.query(`
                UPDATE data_product 
                SET 
                    url = IFNULL(:url, url), 
                    next_url = :next_url, 
                    data = :data, 
                    pending = NULL 
                WHERE id = :id`,
                {
                    id: data.id,
                    url: result.url,
                    next_url: result.next_url, 
                    data: data.data,
                }
            );    
        }
        return this.mysql.query(`
            UPDATE data_product 
            SET 
                url = IFNULL(:url, url), 
                next_url = NULL, 
                data = :data, 
                complete = NOW(), 
                status = :status 
            WHERE id = :id`,
            {
                id: data.id, 
                url: result.url, 
                data: data.data, 
                status: data.data ? 200 : 404,
            }
        );
    }

    setErrorData(source, data, { status }) {
        return this.mysql.query(`
            UPDATE data_product 
            SET 
                url = IFNULL(:url, url), 
                next_url = NULL, 
                data = NULL, 
                complete = NOW(), 
                status = :status 
            WHERE id = :id`,
            {
                id: data.id, 
                url: data.url, 
                status,
            }
        );   
    }

    getResultData() {
        return this.mysql.query(`
            SELECT 
                id, 
                base_product_id, 
                source_id, 
                url, 
                data, 
                status 
            FROM data_product 
            WHERE complete IS NOT NULL 
                AND (transfer IS NULL OR transfer < DATE_SUB(NOW(), INTERVAL 10 MINUTE)) 
            LIMIT 20`
        )
        .then(data => {
            if (data.length) {
                return this.mysql.query(`
                    UPDATE data_product 
                    SET transfer = NOW() 
                    WHERE id IN (:ids)`, 
                    { ids: data.map(row => row.id) }
                )
                .then(() => data);                    
            }
            return null;
        });
    }

    cleanup(ids) {
        return this.mysql.query(`
            DELETE FROM data_product 
            WHERE id IN (:ids)`, 
            { ids }
        );
    }
}

module.exports = (mysql) => new Store(mysql);