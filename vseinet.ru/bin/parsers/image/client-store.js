'use strict';

class Store {
    constructor(mysql) {
        this.mysql = mysql;
    }

    setRequestData(source, data) {
        const insert = `
            INSERT IGNORE INTO data_image (
                id, 
                base_product_id, 
                source_id, 
                url
            ) VALUES `;
        const placeholders = `(
                :id$n, 
                :base_product_id$n, 
                :source_id$n, 
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
                source_id, url 
            FROM data_image 
            WHERE source_id = :source_id 
                AND (pending IS NULL OR (complete IS NULL AND pending < DATE_SUB(NOW(), INTERVAL 10 MINUTE))) 
            ORDER BY IF(pending IS NULL, 1, 2) 
            LIMIT 1`,
            { source_id: source.id }
        )
        .then(row => {
            if (row) {
                return this.mysql.query(`
                    UPDATE data_image 
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
        return this.mysql.query(`
            UPDATE data_image 
            SET 
                image_base_64 = :image_base_64, 
                complete = NOW(), 
                status = :status 
            WHERE id = :id`,
            { 
                id: data.id, 
                status: result.imageBase64 ? 200 : 404, 
                image_base_64: result.imageBase64 
            }
        );
    }

    setErrorData(source, data, { status }) {
        return this.mysql.query(`
            UPDATE data_image 
            SET 
                complete = NOW(), 
                status = :status 
            WHERE id = :id`,
            {   id: data.id,
                status,
            }
        );   
    }

    getResultData() {
        return this.mysql.query(`
            SELECT 
                id, 
                base_product_id, 
                image_base_64, 
                status 
            FROM data_image 
            WHERE complete IS NOT NULL 
                AND (transfer IS NULL OR transfer < DATE_SUB(NOW(), INTERVAL 10 MINUTE)) 
            LIMIT 20`
        )
        .then(data => {
            if (data.length) {
                return this.mysql.query(`
                    UPDATE data_image 
                    SET transfer = NOW() 
                    WHERE id IN (:ids)`, 
                    { ids: data.map(row => row.id )}
                )
                .then(() => data);
            }
            return null;
        });
    }

    cleanup(ids) {
        return this.mysql.query(`
            DELETE FROM data_image 
            WHERE id IN (:ids)`, 
            { ids }
        );
    }
}

module.exports = (mysql) => new Store(mysql);