'use strict';

class Store {
    constructor(mysql) {
        this.mysql = mysql;
    }

    getActiveSources() {
        return this.mysql.query(`
            SELECT 
                id, alias name, 
                is_active, 
                0 anti_guard, 
                points_link url 
            FROM trading 
            WHERE is_active`
        );
    }

    getSource(id) {
        return this.mysql.queryRow(`
            SELECT 
                id, 
                alias name, 
                is_active, 
                0 anti_guard, 
                points_link url 
            FROM trading 
            WHERE id = :id`, 
            { id }
        );
    }

    getRequestData(sourceId) {
        return this.mysql.query(`
            SELECT 
                id,
                points_link url 
            FROM trading 
            WHERE (pending IS NULL OR DATEDIFF(NOW(), pending) > 1) 
                AND (last_update IS NULL OR DATEDIFF(NOW(), last_update) > 1)`
        )
        .then(data => {
            if (data.length) {
                const promises = data.map(row => 
                    this.mysql.query(`
                        UPDATE trading
                        SET pending = NOW()
                        WHERE id = :id`, 
                        row
                    );
                );
                return Promise.all(promises).then(() => data);
            }
            return null;
        });
    }

    setResultData(data) {
        const promises = data.map(row => 
            this.mysql.query(`
                UPDATE trading 
                SET 
                    pending = NULL,
                    last_update = NOW
                WHERE id = :id`,
                row
            )
            .then(() =>
                this.mysql.query(`
                    UPDATE trading_point
                    SET is_active = 0
                    WHERE trading_id = :id`,
                    row
                )
                .then(() => {
                    const points = JSON.parse(row.points);
                    const insert = `
                        INSERT INTO trading_point (
                            native_id, 
                            native_name, 
                            trading_id, 
                            native_link, 
                            is_active
                        ) VALUES `;
                    const placeholders = `(
                            :native_id$n, 
                            :native_name$n, 
                            :trading_id$n, 
                            :native_link$n, 
                            1
                        )`;
                    const duplicate = `
                        ON DUPLICATE KEY UPDATE 
                            native_link = :native_link, 
                            is_active = 1`;
                    const values = [];
                    const parameters = {};
                    points.map((point, index) => {
                        for (let [ key, value ] of point) {
                            parameters[key + index] = value;
                        }
                        values.push(placeholders.replace(/\$n/g, index));
                    });
                    return this.mysql.query(insert + values.join(', ') + duplicate)
                        .then(() => 
                            this.mysql.query(`
                                UPDATE city c  
                                INNER JOIN trading_point tp ON tp.native_name = c.name  
                                SET tp.city_id = c.id 
                                WHERE tp.trading_id = :id AND tp.city_id = 0 AND tp.is_active = 1`,
                                row
                            )
                        );
                });
            );
        );
        return Promise.all(promises); 
    }
}

module.exports = (mysql) => new Store(mysql);