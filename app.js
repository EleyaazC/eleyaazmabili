const express = require('express');
const cors = require('cors');
const app = express();
const listingsRoutes = require('./server/routes');

app.use(cors());
app.use(express.static('public'));
app.use('/api', listingsRoutes);

const PORT = 3000;
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});