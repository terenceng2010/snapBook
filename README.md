# snapBook
Service to remove facebook status after certain period

# Why
Sometimes I don't want my facebook status to exist forever because I may have said something inapproriate 
or I don't want people to stalk my Facebook profile.

# How to use

1. Run the attached sql creation script to create the sql database on MYSQL
2. Deploy the server side to a PHP host. 
3. You would need to register a FB app account and change the FacebookSession::setDefaultApplication value in UserDao.php
4. Make sure the php server can access the mysql databse.

