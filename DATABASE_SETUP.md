# Database Setup Instructions

## Problem Resolved

The error `SQLSTATE[42000]: Syntax error or access violation: 1305 PROCEDURE browsergame.UpgradeBuilding does not exist` has been fixed by:

1. **Adding the missing `UpgradeBuilding` stored procedure**
2. **Creating both `quizgame` and `browsergame` databases for compatibility**
3. **Ensuring all required tables and procedures are available in both databases**

## Setup Instructions

To initialize the database and resolve the error, follow these steps:

### 1. Run the SQL Setup Script

Execute the updated `QuizgameSQL.sql` file to create the databases and all required tables/procedures:

```bash
mysql -u root -p < QuizgameSQL.sql
```

### 2. Verify Database Setup

The script will create:

- **quizgame** database (primary database used by the application)
- **browsergame** database (compatibility database for legacy references)
- Both databases contain the same schema including the `UpgradeBuilding` procedure

### 3. Database Configuration

Ensure your MySQL server is running and accessible at the configured host (`172.17.0.7` in database.php).

If you need to change the database connection settings, update the following in `html/php/database.php`:

```php
private $host = '172.17.0.7';        // Your MySQL host
private $dbname = 'quizgame';        // Database name  
private $username = 'quizgame';      // Database user
private $password = 'sicheresPasswort'; // Database password
```

### 4. Test Database Connection

You can test the database setup using:

```bash
php test_database_connection.php
```

## What Was Fixed

### Missing Procedure
The `UpgradeBuilding` procedure was added with the following signature:
```sql
CREATE PROCEDURE UpgradeBuilding(
    IN p_PlayerID INT,
    IN p_BuildingType VARCHAR(100), 
    IN p_Level INT
)
```

This procedure:
- Checks if a player has enough points for an upgrade
- Deducts the upgrade cost from player points
- Returns success/failure status and remaining points

### Database Compatibility
- Both `quizgame` and `browsergame` databases are now created
- The user has permissions on both databases
- All tables and procedures exist in both databases
- This resolves any legacy code that might reference the `browsergame` database

## Error Resolution

The original error should now be resolved because:

1. ✅ The `UpgradeBuilding` procedure now exists
2. ✅ Both database names (`quizgame` and `browsergame`) are supported
3. ✅ All required tables and procedures are properly created
4. ✅ Database permissions are correctly set

After running the SQL setup script, the error `PROCEDURE browsergame.UpgradeBuilding does not exist` should no longer occur.