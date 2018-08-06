'use strict';

class Store {
    constructor(mysql) {
        this.mysql = mysql;
    }

    getActiveSources() {
        return this.mysql.query(`
            SELECT 
                id, 
                CONCAT(code, '_', alias) name, 
                site, 
                supplier_id, 
                active is_active, 
                anti_guard
            FROM source 
            WHERE active AND supplier_id = 62`
        );
    }

    getSource(id) {
        return this.mysql.query(`
            SELECT 
                id, 
                CONCAT(code, '_', alias) name, 
                site, 
                supplier_id, 
                active is_active, 
                anti_guard 
            FROM source 
            WHERE id = :id AND supplier_id = 62`,
            { id }
        );
    }

    getRequestData(source) {
        return this.mysql.query(`
            SELECT 
                p.id, 
                s.id source_id, 
                sp.id supplier_product_id, 
                sp.name, 
                sp.code, 
                sp.ccode, 
                sp.artikul, 
                IF(sp.url = '', NULL, sp.url) url 
            FROM supplier_product sp 
            INNER JOIN source s ON s.supplier_id = sp.supplier_id 
            LEFT OUTER JOIN source_supplier_product p ON p.source_id = s.id AND p.supplier_product_id = sp.id 
            WHERE s.id = :source_id  AND sp.base_product_id = 0 AND sp.is_available AND (p.id IS NULL OR (p.completed_date IS NULL AND p.pending_date < DATE_SUB(NOW(), INTERVAL 30 MINUTE))) 
            LIMIT 10`,
            { source_id: source.id }
        )
        .then(data => {
            if (data.length) {
                const promises = data.map(row => 
                    this.mysql.query(`
                        INSERT INTO source_supplier_product (
                            source_id, 
                            supplier_product_id, 
                            name, 
                            url, 
                            pending_date
                        ) 
                        VALUES (
                            :source_id, 
                            :supplier_product_id, 
                            :name, 
                            :url, 
                            NOW()
                        )
                        ON DUPLICATE KEY UPDATE 
                            id = LAST_INSERT_ID(id), 
                            pending_date = NOW(), 
                            attempt = attempt + 1`,
                        row
                    )
                    .then(result => ({
                        ...row, 
                        id: result.insertId,
                    }))
                );
                return Promise.all(promises);
            }
            return null;
        });
    }

    setResultData(data) {
        const promises = data.map(row => 
            this.mysql.query(`
                UPDATE source_supplier_product
                SET 
                    url = :url, 
                    status = :status, 
                    data = :data, 
                    completed_date = NOW() 
                WHERE id = :id`,
                row
            )
            .then(() => row.id)
        );
        return Promise.all(promises);
    }
}

module.exports = (mysql) => new Store(mysql);