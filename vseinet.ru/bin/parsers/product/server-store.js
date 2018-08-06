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
            WHERE active`
        );
    }

    getSource(id) {
        return this.mysql.queryRow(`
            SELECT 
                id, 
                CONCAT(code, '_', alias) name, 
                site, 
                supplier_id, 
                active is_active, 
                anti_guard 
            FROM source 
            WHERE id = :id`, 
            { id }
        );
    }

    getRequestData(source) {
        return Promise.resolve().then(() => {
            if (source.supplier_id) {
                return this.mysql.query(`
                    SELECT 
                        p.id, 
                        s.id source_id, 
                        sp.base_product_id, 
                        sp.name, 
                        sp.code, 
                        sp.ccode, 
                        sp.artikul, 
                        IF(sp.url = '', NULL, sp.url) url
                    FROM supplier_product sp
                    INNER JOIN base_product bp ON bp.id = sp.base_product_id 
                    INNER JOIN source s ON s.supplier_id = sp.supplier_id 
                    LEFT OUTER JOIN source_product p ON p.source_id = s.id AND p.base_product_id = bp.id 
                    WHERE s.id = :source_id AND sp.is_available AND (p.id IS NULL OR (p.completed_date IS NULL AND p.pending_date < DATE_SUB(NOW(), INTERVAL 30 MINUTE))) 
                    LIMIT 10`,
                    { source_id: source.id }
                );
            }
            return this.mysql.query(`
                SELECT 
                    p.id, 
                    s.id source_id, 
                    bp.id base_product_id, 
                    IF (b.name IS NOT NULL AND bpdt.model IS NOT NULL, CONCAT(b.name, ' ', bpdt.model), bp.name) name, 
                    NULL code, 
                    NULL ccode, 
                    NULL artikul, 
                    NULL url 
                FROM base_product bp 
                INNER JOIN source s ON s.id = :source_id 
                LEFT OUTER JOIN base_product_image bpi ON bpi.base_product_id = bp.id AND bpi.priority = 1
                LEFT OUTER JOIN source_product p ON p.source_id = s.id AND p.base_product_id = bp.id 
                LEFT OUTER JOIN supplier_product sp ON sp.base_product_id = bp.id AND sp.supplier_id = 218 
                LEFT OUTER JOIN base_product_data bpdt ON bpdt.id = bp.id 
                LEFT OUTER JOIN brand b ON b.id = bp.brand_id 
                WHERE bp.is_available AND bpi.id IS NULL AND sp.id IS NULL AND (p.id IS NULL OR (p.completed_date IS NULL AND p.pending_date < DATE_SUB(NOW(), INTERVAL 30 MINUTE))) 
                LIMIT 10`,  
                { source_id: source.id } 
            );
        })
        .then(data => {
            if (data.length) {
                const promises = data.map(row =>
                    this.mysql.query(`
                        INSERT INTO source_product (
                            source_id, 
                            base_product_id, 
                            name, 
                            url, 
                            pending_date
                        ) VALUES (
                            :source_id,
                            :base_product_id, 
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
        const promises = data.map(row => {
            let data = row.data ? JSON.parse(row.data.replace(/[\u0000-\u001F]/g, '')) : {};
            if (data.name) {
                row.name = data.name;
            }
            else {
                if (!row.name) {
                    row.name = null;
                }
                if (row.status === 200) {
                    data.status = 204;
                }
            }
            row.brand = data.brand || null;
            row.model = data.model || null; 
            row.images = data.images && data.images.length ? JSON.stringify(data.images) : null; 
            row.details = data.details && data.details.length ? JSON.stringify(data.details) : null; 
            row.description = data.description || null;
            return new Promise(fulfill => {
                this.mysql.query(`
                    UPDATE source_product
                    SET 
                        url = :url, 
                        status = :status, 
                        completed_date = NOW(), 
                        name = IFNULL(:name, name), 
                        brand = IFNULL(:brand, brand), 
                        model = IFNULL(:model, model), 
                        images = IFNULL(:images, images), 
                        details = IFNULL(:details, details), 
                        description = IFNULL(:description, description) 
                    WHERE id = :id`,
                    row
                )
                .then(() => {
                    if (data.images.length) {
                        const insert = `
                            INSERT IGNORE INTO source_image (
                                source_id,
                                source_product_id,
                                base_product_id,
                                url,
                                url_hash
                            ) VALUES ( `;
                        const placeholders = `
                                :source_id,
                                :source_product_id,
                                :base_product_id,
                                :url$n,
                                MD5(:url$n)
                            )`;
                        const values = [];
                        const parameters = {
                            source_id: row.source_id,
                            source_product_id: row.id,
                            base_product_id: row.base_product_id,
                        };
                        data.images.map((image, index) => {
                            parameters['url' + index] = image;
                            values.push(placeholders.replace(/\$n/g, index));
                        });
                        return this.mysql.query(insert + values.join(', '), parameters);
                    }
                    return row.id;
                })
                .then(() => 
                    fulfill(row.id)
                )
                .catch(err => 
                    fulfill(null)
                );
            });
        });
        return new Promise(fulfill => {
            Promise.all(promises).then(ids => fulfill(ids.filter(id => id !== null)));   
        });
    }
}

module.exports = (mysql) => new Store(mysql);