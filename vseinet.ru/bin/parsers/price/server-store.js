'use strict';

class Store {
    constructor(mysql) {
        this.mysql = mysql;
    }

    getActiveSources() {
        return this.mysql.query(`
            SELECT 
                id, 
                alias name, 
                is_active, 
                0 anti_guard 
            FROM competitor 
            WHERE is_active`
        );
    }

    getSource(id) {
        return this.mysql.queryRow(`
            SELECT 
                id, 
                alias name, 
                is_active, 
                0 anti_guard 
            FROM competitor 
            WHERE id = :id`,
            { id }
        );
    }

    getRequestData(sourceId) {
        return this.mysql.query(`
            SELECT 
                ptc.id, 
                p.base_product_id, 
                ptc.competitor_id source_id, 
                ptc.link url, 
                ptc.product_id, 
                ptc.request 
            FROM product_to_competitor ptc 
            INNER JOIN product p ON p.id = ptc.product_id 
            WHERE ptc.link != '' AND p.is_active > 1 AND ptc.competitor_id = :source_id 
                AND (ptc.request OR DATEDIFF(NOW(), ptc.date) >= 1 OR ptc.date IS NULL) 
                AND (ptc.pending IS NULL OR ptc.pending < DATE_SUB(NOW(), INTERVAL 10 MINUTE)) 
            ORDER BY IF(ptc.request, 1, 2), ptc.date, IFNULL(ptc.pending, 0) 
            LIMIT 10`,
            { source_id: sourceId }
        )
        .then(data => {
            if (data.length) {
                return this.mysql.query(`
                    UPDATE product_to_competitor 
                    SET pending = NOW() 
                    WHERE id IN (:ids)`, 
                    { ids: data.map(row => row.id)}
                )
                .then(() => data);
            }
            return null;
        });
    }

    setResultData(data) {
        const promises = data.map(row => {
            return this.mysql.query(`
                UPDATE product_to_competitor 
                SET 
                    date = NOW(), 
                    price = :price, 
                    request = 0, 
                    pending = null, 
                    status = :status 
                WHERE id = :id`,
                row
            );
        });
        return Promise.all(promises);
    }
}

module.exports = (mysql) = new Store(mysql);