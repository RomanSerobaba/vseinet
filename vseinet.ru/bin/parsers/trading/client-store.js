'use strict';

class Store {
    constructor(mysql) {
        this.mysql = mysql;
    }

    setRequestData(source, data) {
        return this.mysql.query(`
            INSERT INTO data_trading (
                id,
                name,
                url
            ) VALUES (
                :id,
                :name,
                :url
            )
            ON DUPLICATE KEY UPDATE 
                url = VALUES(url),
                pending = NULL,
                complete = NULL`,
            source
        );
    }

    getRequestData(source) {
        return this.mysql.queryRow(`
            SELECT 
                id,
                name,
                url
            FROM data_trading
            WHERE id = :source_id 
                AND (pending IS NULL OR (complete IS NULL AND pending < DATE_SUB(NOW(), INTERVAL 30 MINUTE)))`,
            { source_id: source.id }
        )
        .then(data => {
            if (data) {
                this.mysql.query(`
                    UPDATE data_trading 
                    SET pending = NOW() 
                    WHERE id = :id`, 
                    data
                )
                .then(() => data);
            }
            return null;
        });
    }

    setResultData(source, data) {
        return this.mysql.query(`
            UPDATE data_trading
            SET 
                complete = NOW(), 
                status = 200,
                points = :points
            WHERE id = :source_id`,
            { 
                source_id: source.id,
                points: JSON.stringify(data),
            }
        );
    }

    setErrorData(source, data, { status }) {
        return this.mysql.query(`
            UPDATE data_trading 
            SET  
                points = NULL, 
                complete = NOW(), 
                status = :status 
            WHERE id = :id`,
            {
                id: data.id, 
                status,
            }
        );   
    }

    getResultData() {
        return this.mysql.query(`
            SELECT 
                id, 
                points
            FROM data_trading 
            WHERE complete IS NOT NULL 
                AND (transfer IS NULL OR transfer < DATE_SUB(NOW(), INTERVAL 10 MINUTE)) 
        `)
        .then(data => {
            if (data.length) {
                return this.mysql.query(`
                    UPDATE data_trading 
                    SET transfer = NOW() 
                    WHERE id IN (:ids)`, 
                    { ids: data.map(row => row.id)}
                )
                .then(() => data);
            }
        });
    }

    cleanup(ids) {
        return this.mysql.query(`
            DELETE FROM data_trading 
            WHERE id IN (:ids)`, 
            { ids }
        );
    }
}

module.exports = (mysql) => new Store(mysql);