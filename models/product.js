const { getConnection, Request, TYPES } = require('../db');

// Existing getProducts and getProductById functions (as before)

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

const updateProduct = async (id, productData) => {
    const connection = getConnection();
    return new Promise((resolve, reject) => {
        connection.on('connect', (err) => {
            if (err) {
                console.error('Connection error:', err);
                reject(err);
            } else {
                const updates = [];
                if (productData.Name) updates.push('Name = @Name');
                if (productData.ProductNumber) updates.push('ProductNumber = @ProductNumber');
                if (productData.StandardCost !== undefined) updates.push('StandardCost = @StandardCost');
                if (productData.ListPrice !== undefined) updates.push('ListPrice = @ListPrice');
                if (productData.SellStartDate) updates.push('SellStartDate = @SellStartDate');

                if (updates.length === 0) {
                    connection.close();
                    resolve(0); // No updates provided
                    return;
                }

                const sql = `UPDATE SalesLT.Product SET ${updates.join(', ')} WHERE ProductID = @ProductID`;
                const request = new Request(sql, (err, rowCount) => {
                    if (err) {
                        console.error(`Error updating product ${id}:`, err);
                        connection.close();
                        reject(err);
                    } else {
                        console.log(`${rowCount} row(s) updated for ProductID: ${id}`);
                        connection.close();
                        resolve(rowCount);
                    }
                });

                request.addParameter('ProductID', TYPES.Int, parseInt(id));
                if (productData.Name) request.addParameter('Name', TYPES.NVarChar, productData.Name);
                if (productData.ProductNumber) request.addParameter('ProductNumber', TYPES.NVarChar, productData.ProductNumber);
                if (productData.StandardCost !== undefined) request.addParameter('StandardCost', TYPES.Money, productData.StandardCost);
                if (productData.ListPrice !== undefined) request.addParameter('ListPrice', TYPES.Money, productData.ListPrice);
                if (productData.SellStartDate) request.addParameter('SellStartDate', TYPES.DateTime2, productData.SellStartDate);

                connection.execSql(request);
            }
        });
        connection.connect();
    });
};

const deleteProduct = async (id) => {
    const connection = getConnection();
    return new Promise((resolve, reject) => {
        connection.on('connect', (err) => {
            if (err) {
                console.error('Connection error:', err);
                reject(err);
            } else {
                const request = new Request(
                    `DELETE FROM SalesLT.Product WHERE ProductID = @ProductID`,
                    (err, rowCount) => {
                        if (err) {
                            console.error(`Error deleting product ${id}:`, err);
                            connection.close();
                            reject(err);
                        } else {
                            console.log(`${rowCount} row(s) deleted for ProductID: ${id}`);
                            connection.close();
                            resolve(rowCount);
                        }
                    }
                );
                request.addParameter('ProductID', TYPES.Int, parseInt(id));
                connection.execSql(request);
            }
        });
        connection.connect();
    });
};

module.exports = { getProducts, getProductById, createProduct, updateProduct, deleteProduct };
