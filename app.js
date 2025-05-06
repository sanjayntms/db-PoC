// app.js
const express = require('express');
const app = express();
const port = process.env.PORT || 3000;
require('dotenv').config();
const productModel = require('./models/product');

app.use(express.json()); // Middleware to parse JSON request bodies
app.use(express.static('public'));

// GET all products
app.get('/api/products', async (req, res) => {
    try {
        const products = await productModel.getProducts();
        res.json(products);
    } catch (error) {
        console.error('Error fetching products:', error);
        res.status(500).json({ error: 'Failed to retrieve products' });
    }
});

// GET product by ID
app.get('/api/products/:id', async (req, res) => {
    const productId = req.params.id;
    try {
        const product = await productModel.getProductById(productId);
        if (product) {
            res.json(product);
        } else {
            res.status(404).json({ error: `Product with ID ${productId} not found` });
        }
    } catch (error) {
        console.error(`Error fetching product with ID ${productId}:`, error);
        res.status(500).json({ error: 'Failed to retrieve product' });
    }
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

app.get('/', (req, res) => {
    res.send('Welcome to the AdventureWorks API!');
});

app.listen(port, () => {
    console.log(`REST API server listening on port ${port}`);
});
