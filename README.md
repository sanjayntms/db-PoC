# db-PoC
🔧 Step 1: Set App Settings in Azure
1️⃣ Go to your App Service in the Azure Portal.
2️⃣ Under Settings → Configuration → Application settings, add the following:

Name	Value
* DB_SERVER	tcp:ntmsdb11.database.windows.net,1433
* DB_NAME	db
* DB_USER	sqladmin
* DB_PASSWORD	password
[
  {
    "name": "DB_NAME",
    "value": "db",
    "slotSetting": false
  },
  {
    "name": "DB_PASSWORD",
    "value": "password",
    "slotSetting": false
  },
  {
    "name": "DB_SERVER",
    "value": "tcp:ntmsdb11.database.windows.net,1433",
    "slotSetting": false
  },
  {
    "name": "DB_USER",
    "value": "sqladmin",
    "slotSetting": false
  }
]
# 
## Modify Seed if you want to insert huge data for testing purpose like DTU,vcore perf
# In database,
* CREATE USER appuser WITH PASSWORD = 'your password';
* GRANT SELECT, INSERT, DELETE TO appuser;
*  For data masking, DROP VIEW SalesLT.vProductAndDescription;
   and then reload web page, in name column , value should be masked and viewed as xxxx

   
