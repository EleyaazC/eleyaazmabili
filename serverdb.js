const mysql = require('mysql2');

const pool = mysql.createPool({
  host: 'localhost',
  user: 'your_mysql_user',
  password: 'your_mysql_password',
  database: 'rental_db'
});

module.exports = pool.promise();