// Example: models/product.js
const { getConnection, Request, TYPES } = require('../db');

const getProducts = async () => {
    const connection = getConnection();
    return new Promise((resolve, reject) => {
        connection.on('connect', (err) => {
            if (err) {
                console.error('Connection error:', err);
                reject(err);
            } else {
                const products = [];
                const request = new Request("SELECT ProductID, Name, ListPrice FROM SalesLT.Product", (err, rowCount) => {
                    if (err) {
                        console.error('Query error:', err);
                        connection.close();
                        reject(err);
                    } else {
                        console.log(`${rowCount} row(s) returned`);
                        connection.close();
                        resolve(products);
                    }
                });

                request.on('row', (columns) => {
                    const product = {};
                    columns.forEach((column) => {
                        product[column.metadata.colName] = column.value;
                    });
                    products.push(product);
                });

                connection.execSql(request);
            }
        });

        connection.connect();
    });
};

module.exports = { getProducts };
