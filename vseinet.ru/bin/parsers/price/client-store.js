'use strict';

class Store {
    constructor(mysql) {
        this.mysql = mysql;
    }

    setRequestData(source, data) {
        const sql = `
            INSERT IGNORE INTO data_price (
                id, 
                base_product_id, 
                source_id, 
                url, 
                product_id, 
                request
            ) VALUES `;
        const placeholders = `(
                :id$n, 
                :base_product_id$n, 
                :source_id$n, 
                :url$n, 
                :product_id$n, 
                :request$n
            )`;    
        const values = [];
        const parameters = {};
        data.map((row, index) => {
            for (let [ key, value ] of row) {
                parameters[key + index] = value;
            }
            values.push(placeholders.replace(/\$n/g, index));
        });
        return this.mysql.query(sql + values.join(', '), parameters);
    }

    getRequestData(source) {
        return this.mysql.queryRow(`
            SELECT 
                id, 
                base_product_id, 
                source_id, 
                url 
            FROM data_price  
            WHERE source_id = :source_id 
                AND (pending IS NULL OR (complete IS NULL AND pending < DATE_SUB(NOW(), INTERVAL 10 MINUTE)))  
            ORDER BY IF(request, 1, 2), IF(complete IS NULL, 1, 2)  
            LIMIT 1`,
            { source_id: source.id }
        )
        .then(data => {
            if (data) {
                return this.mysql.query(`
                    UPDATE data_price 
                    SET pending = NOW() 
                    WHERE id = :id`, 
                    data
                )
                .then(() => data);
            }
            return null;
        });
    }

    setResultData(source, data, result) {
        return this.mysql.query(`
            UPDATE data_price 
            SET 
                price = :price, 
                complete = NOW(), 
                status = :status 
            WHERE id = :id`,
            { 
                price: 100 * (parseFloat(result.price) || 0), 
                status: 200, 
                id: data.id 
            }
        ); 
    }

    getResultData() {
        return this.mysql.query(`
            SELECT 
                id, 
                base_product_id, 
                source_id, 
                product_id, 
                price, 
                status 
            FROM data_price 
            WHERE complete IS NOT NULL 
                AND (transfer IS NULL OR transfer < DATE_SUB(NOW(), INTERVAL 2 MINUTE)) 
            ORDER BY IF(request, 1, 2), IF(transfer IS NOT NULL, 1, 2) 
            LIMIT 20`
        )
        .then(data => { 
            if (data.length) {
                return this.mysql.query(`
                    UPDATE data_price 
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
            DELETE FROM data_price 
            WHERE id IN (:ids)`, 
            { ids }
        );
    }
}

module.exports = (mysql) => new Store(mysql);