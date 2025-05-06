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

const getProductById = async (productId) => {
    const connection = getConnection();
    return new Promise((resolve, reject) => {
        connection.on('connect', (err) => {
            if (err) {
                console.error('Connection error:', err);
                reject(err);
            } else {
                let product = null;
                const request = new Request("SELECT ProductID, Name, ListPrice FROM SalesLT.Product WHERE ProductID = @productId", (err, rowCount) => {
                    if (err) {
                        console.error('Query error:', err);
                        connection.close();
                        reject(err);
                    } else {
                        console.log(`${rowCount} row(s) returned for product ID ${productId}`);
                        connection.close();
                        resolve(product);
                    }
                });

                request.addParameter('productId', TYPES.Int, parseInt(productId));

                request.on('row', (columns) => {
                    product = {};
                    columns.forEach((column) => {
                        product[column.metadata.colName] = column.value;
                    });
                });

                connection.execSql(request);
            }
        });

        connection.connect();
    });
};

module.exports = { getProducts, getProductById };
