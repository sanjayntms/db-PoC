const express = require('express');
const app = express();
const port = process.env.PORT || 3000;
require('dotenv').config();
const productModel = require('./models/product');

app.use(express.json());
app.use(express.static('public')); // Serve static files from the 'public' directory

// ... your API routes (/api/products, etc.) ...

app.get('/', (req, res) => {
    res.send('Welcome to the AdventureWorks API!');
});

app.listen(port, () => {
    console.log(`REST API server listening on port ${port}`);
});
