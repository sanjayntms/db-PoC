const express = require('express');
const app = express();
const port = process.env.PORT || 3000;
require('dotenv').config();
const productModel = require('./models/product');

app.use(express.json()); // Middleware to parse JSON request bodies
app.use(express.static('public'));

// GET all products
app.get('/api/products', async (req, res) => {
    // ... (your existing get products code) ...
});

// GET product by ID
app.get('/api/products/:id', async (req, res) => {
    // ... (your existing get product by ID code) ...
});

// POST - Create a new product
app.post('/api/products', async (req, res) => {
    try {
        const newProductId = await productModel.createProduct(req.body);
        res.status(201).json({ message: 'Product created successfully', productId: newProductId });
    } catch (error) {
        console.error('Error creating product:', error);
        res.status(500).json({ error: 'Failed to create product' });
    }
});

// PUT - Update an existing product by ID
app.put('/api/products/:id', async (req, res) => {
    const productId = req.params.id;
    try {
        const rowsAffected = await productModel.updateProduct(productId, req.body);
        if (rowsAffected > 0) {
            res.json({ message: `Product ${productId} updated successfully` });
        } else {
            res.status(404).json({ error: `Product with ID ${productId} not found or no updates applied` });
        }
    } catch (error) {
        console.error(`Error updating product ${productId}:`, error);
        res.status(500).json({ error: `Failed to update product ${productId}` });
    }
});

// DELETE - Delete a product by ID
app.delete('/api/products/:id', async (req, res) => {
    const productId = req.params.id;
    try {
        const rowsAffected = await productModel.deleteProduct(productId);
        if (rowsAffected > 0) {
            res.json({ message: `Product ${productId} deleted successfully` });
        } else {
            res.status(404).json({ error: `Product with ID ${productId} not found` });
        }
    } catch (error) {
        console.error(`Error deleting product ${productId}:`, error);
        res.status(500).json({ error: `Failed to delete product ${productId}` });
    }
});

app.get('/', (req, res) => {
    res.send('Welcome to the AdventureWorks API!');
});

app.listen(port, () => {
    console.log(`REST API server listening on port ${port}`);
});
