// models/product.js
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

const createProduct = async (productData) => {
    const connection = getConnection();
    return new Promise((resolve, reject) => {
        connection.on('connect', (err) => {
            if (err) {
                console.error('Connection error:', err);
                reject(err);
            } else {
                const request = new Request(
                    `INSERT INTO SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, SellStartDate)
                     OUTPUT INSERTED.ProductID
                     VALUES (@Name, @ProductNumber, @StandardCost, @ListPrice, @SellStartDate)`,
                    (err, rowCount, rows) => {
                        if (err) {
                            console.error('Error creating product:', err);
                            connection.close();
                            reject(err);
                        } else {
                            const newProductId = rows[0][0].value;
                            console.log(`${rowCount} row(s) inserted, ProductID: ${newProductId}`);
                            connection.close();
                            resolve(newProductId);
                        }
                    }
                );

                request.addParameter('Name', TYPES.NVarChar, productData.Name);
                request.addParameter('ProductNumber', TYPES.NVarChar, productData.ProductNumber || null); // Optional
                request.addParameter('StandardCost', TYPES.Money, productData.StandardCost || 0);       // Optional
                request.addParameter('ListPrice', TYPES.Money, productData.ListPrice);
                request.addParameter('SellStartDate', TYPES.DateTime2, productData.SellStartDate || new Date()); // Default to now

                connection.execSql(request);
            }
        });
        connection.connect();
    });
};

module.exports = { getProducts, getProductById, createProduct };
