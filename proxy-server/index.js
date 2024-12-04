const express = require('express');
const axios = require('axios');  // Import axios
const app = express();

app.use(express.json());  

app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    if (req.method === 'OPTIONS') {
      return res.status(200).end();
    }
    next();
});

app.use('/api/libraries', async (req, res) => {
    try {
      const response = await axios({
        method: req.method,
        url: `http://localhost:8888/api/libraries?${new URLSearchParams(req.query)}`,  // Forward query params
        headers: req.headers,
        data: req.body
      });
      res.json(response.data);
    } catch (error) {
      console.error('Error during proxy request:', error);
      res.status(500).json({ error: 'Something went wrong while fetching libraries.' });
    }
});

app.use('/api/books', async (req, res) => {
    try {
      const response = await axios({
        method: req.method,
        url: `http://localhost:8888/api/books?${new URLSearchParams(req.query)}`,  // Forward query params
        headers: req.headers,
        // Only include the body for POST requests
        data: req.method === 'POST' ? req.body : undefined
      });
      res.json(response.data);
    } catch (error) {
      console.error('Error during proxy request:', error);
      res.status(500).json({ error: 'Something went wrong while fetching books.' });
    }
});

// Start the server
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Proxy server running at http://localhost:${PORT}`);
});
