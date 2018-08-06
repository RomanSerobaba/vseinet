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
                (active AND parse_images) is_active, 
                anti_guard 
            FROM source 
            WHERE active AND parse_images    
        `);
    }

    getSource(id) {
        return this.mysql.query(`
            SELECT 
                id, 
                CONCAT(code, '_', alias) name, 
                site, 
                (active AND parse_images) is_active, 
                anti_guard  
            FROM source 
            WHERE id = :id`, 
            { id }
        );
    }

    getRequestData(source) {
        return this.mysql.query(`
            SELECT 
                id, 
                source_id, 
                base_product_id, 
                url
            FROM source_image 
            WHERE source_id = :source_id 
                AND (pending_date IS NULL OR (completed_date IS NULL AND pending_date < DATE_SUB(NOW(), INTERVAL 30 MINUTE)))
            ORDER BY IF(pending_date IS NULL, 1, 2) 
            LIMIT 10`,
            { source_id: source.id }
        ).then(data => {
            if (data.length) {
                return this.mysql.query(`
                    UPDATE source_image 
                    SET pending_date = NOW() 
                    WHERE id IN (:ids)`, 
                    { ids: data.map(row => row.id) }
                )
                .then(() => data);
            }
            return null;
        });
    }

    setResultData(data) {
        const promises = data.map(row => {
            return new Promise(fulfill => {
                this.mysql.query(`
                    UPDATE source_image 
                    SET 
                        status = :status, 
                        success = :success, 
                        completed_date = NOW() 
                    WHERE id = :id`,
                    { 
                        id: row.id,
                        status: 204, 
                        success: row.image_base_64 ? 1 : 0,
                    } 
                )
                .then(() => {
                    if (row.image_base_64) {
                        return this.mysql.query(`
                            INSERT IGNORE INTO source_image_data (
                                id,
                                base64
                            ) VALUES (
                                :id,
                                :base64 
                            )`,
                            {
                                id: row.id,
                                base64: row.image_base_64,
                            }
                        );
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