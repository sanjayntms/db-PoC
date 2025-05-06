const tedious = require('tedious');

const config = {
    server: process.env.DB_SERVER,
    authentication: {
        type: 'default',
        options: {
            userName: process.env.DB_USER,
            password: process.env.DB_PASSWORD
        }
    },
    options: {
        database: process.env.DB_DATABASE,
        encrypt: process.env.DB_ENCRYPT === 'true',
        trustServerCertificate: false // Only set to true in non-production environments with caution
    }
};

const getConnection = () => {
    return new tedious.Connection(config);
};

module.exports = { getConnection, Request: tedious.Request, TYPES: tedious.TYPES };
