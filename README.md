# AWS Multi-Tier Architecture Deployment Project Steps
This project deploys a three-tier architecture consisting of:
# Architecture Diagram:

<img width="6450" height="6188" alt="Multi-Tier architecture diagram(AWS)" src="https://github.com/user-attachments/assets/cca8f770-75c1-4768-aa7b-310c11d0a7c4" />

# Web Tier (Nginx Web Server)
# Application Tier (PHP Application Server)
# Database Tier (Amazon RDS MySQL)
# Application Load Balancer
# Auto Scaling Groups
# VPC with Public and Private Subnets

Step 1: Create VPC
Create a VPC named:
multi-tier-vpc

CIDR Block:
10.0.0.0/16
<img width="1918" height="1078" alt="vpc_created_successfully1 2" src="https://github.com/user-attachments/assets/679de334-affa-414b-a086-b3896a298895" />

<img width="1918" height="1078" alt="your_vpc1 3" src="https://github.com/user-attachments/assets/054e0cc7-77aa-4a7e-9a2d-25dfd75c470f" />

---------------------------------------------------------------------------------------------------------------------------
Step 2: Create Subnets

Public Subnets
public-subnet-a
10.0.0.0/20
public-subnet-b
10.0.16.0/20

Web Subnets
web-subnet-a
10.0.32.0/20
web-subnet-b
10.0.48.0/20

App Subnets
app-subnet-a
10.0.64.0/20
app-subnet-b
10.0.80.0/20

Database Subnets
db-subnet-a
10.0.96.0/20
db-subnet-b
10.0.112.0/20
<img width="1918" height="1078" alt="subnets_created_successfully1 2" src="https://github.com/user-attachments/assets/4bb37d5d-b8a3-45e7-8fe7-9a334b3f92ee" />
<img width="1918" height="1078" alt="edit_subnet_associations" src="https://github.com/user-attachments/assets/f7ea0e56-2eb9-4208-8924-e63b228771e7" />
<img width="1917" height="1077" alt="explicit_subnet_associations" src="https://github.com/user-attachments/assets/529be154-c285-4a6c-8a83-fe0b7f5ae3ba" />

---------------------------------------------------------------------------------------------------------------------------------------

Step 3: Create Route Tables

Create:
public-rt
private-rt

Associate:
public-subnet-a
public-subnet-b

with:
public-rt

<img width="1918" height="1078" alt="create_route_table1" src="https://github.com/user-attachments/assets/bcd4c319-b707-450d-a9fe-9e58ed0feea0" />
<img width="1918" height="1078" alt="public_rt(route_table)" src="https://github.com/user-attachments/assets/64cc3bad-0d31-4c24-a836-efddd1796cb0" />
<img width="1918" height="1078" alt="edit_routes_public_rt" src="https://github.com/user-attachments/assets/9a3fbf9b-2b30-4816-85b7-701f23498761" />
<img width="1918" height="1078" alt="public_rt_routes" src="https://github.com/user-attachments/assets/7d07ea13-058e-4a2b-8bb7-c4bc30920ece" />

--------------------------------------------------------------------------------------------------------------------------------------------

Step 4: Create Internet Gateway

Create:
multi-tier-igw

Attach to:
multi-tier-vpc

Add Route:
0.0.0.0/0

Target:
Internet Gateway

<img width="1918" height="1078" alt="create_internet_gateway1" src="https://github.com/user-attachments/assets/f76aceef-8893-4c9b-935a-84fb94c513c4" />
<img width="1918" height="1078" alt="successfully_created_internet_gateway_1 1" src="https://github.com/user-attachments/assets/50dddbba-f4c0-49a8-b343-cbee323ca37d" />
<img width="1918" height="1078" alt="multi-tier-igw(internet-gateway)" src="https://github.com/user-attachments/assets/1f742056-17b8-4557-b9de-98c6ee821244" />

--------------------------------------------------------------------------------------------------------------------------------------------

Step 5: Create RDS Subnet Group

Create:
db-subnet-group

Select:
db-subnet-a
db-subnet-b

Availability Zones:
ap-south-1a
ap-south-1b

<img width="1672" height="941" alt="db-subnet-group" src="https://github.com/user-attachments/assets/04cd4f82-6625-4419-8e1f-421f37d241e9" />


-------------------------------------------------------------------------------------------------------------------------------------

Step 6: Create Amazon RDS

Database Engine:
MySQL

Configuration:
Free Tier

Master Username:
root

Instance Type:
db.t3.micro

VPC:
multi-tier-vpc

Create database and copy:
RDS Endpoint

<img width="1918" height="1078" alt="rds_created_successfully" src="https://github.com/user-attachments/assets/f45e663c-67d9-4c75-a822-f41dc227b1f4" />
<img width="1918" height="1078" alt="rds_inbound_rules(3306)" src="https://github.com/user-attachments/assets/a8c03279-dd02-4e2b-87c7-89b3683d8cea" />
<img width="1918" height="1078" alt="rds_inbound_rules_successfully(3306)" src="https://github.com/user-attachments/assets/98f83627-fdf0-4795-946a-f6c385a8235c" />

--------------------------------------------------------------------------------------------------------------------------------------------

Step 7: Create Database EC2 Instance

Launch EC2 Instance.

Network Settings:
VPC = multi-tier-vpc
Subnet = public-subnet-a
Auto Assign Public IP = Enable
Security Group:
HTTP 80
MYSQL 3306
SSH 22
Launch Instance.

--------------------------------------------------------------------------------------------------------------------------------------------

Step 8: Connect Database

Install MySQL Client:-
sudo yum install mariadb105-server -y

Connect to RDS:-
mysql -u root -p -h <RDS-ENDPOINT>

Create Database:-
CREATE DATABASE multitier;
USE multitier;

<img width="1918" height="1078" alt="db_instance_git" src="https://github.com/user-attachments/assets/aaa04a90-9768-4ae8-9eab-d8bf9c043b95" />

-------------------------------------------------------------------------------------------------------------------------------------------

Step 9: Create Application Server

Launch EC2:
Name = app-instance
VPC = multi-tier-vpc
Subnet = app-subnet-a
Auto Assign Public IP = Enable

Use existing:
multi-tier-sg

-------------------------------------------------------------------------------------------------------------------------------------------

Step 10: Configure Application Server

Connect:
ssh -i key.pem ec2-user@<APP-IP>

Install Packages:-
sudo yum install nginx php php-fpm mariadb105-server php-mysqlnd -y

Start Services:-
sudo systemctl start nginx
sudo systemctl enable nginx

sudo systemctl start php-fpm
sudo systemctl enable php-fpm

Move to Website Directory:-
cd /usr/share/nginx/html

Create Database File:-
sudo nano submit.php

Add:

$host="RDS-ENDPOINT";
$dbname="multitier";
$user="root";
$password="password";

Create Form:-
sudo nano form.html

Create PHP Test Page:-
sudo nano index.php

<?php
phpinfo();
?>

Restart Services:-
sudo systemctl restart nginx
sudo systemctl restart php-fpm

-----------------------------------------------------------------------------------------------------------------------------------------

Step 11: Create App AMI

Create Image:
AMI Name = app-ami

Disable:
Reboot Instance

Create Image.

-------------------------------------------------------------------------------------------------------------------------------------------

Step 12: Create App Auto Scaling Group

Create Launch Template:
app-template

AMI:
app-ami

Create ASG:
app-asg

Subnets:
app-subnet-a
app-subnet-b

Settings:
Desired Capacity = 2
Minimum = 1
Maximum = 3

Target Tracking:
50% CPU

Health Check:
ELB Health Check
EBS Health Check
60 Seconds Grace Period

---------------------------------------------------------------------------------------------------------------------------------------------

Step 13: Create Web Server

Launch EC2:
Name = web-server
VPC = multi-tier-vpc
Subnet = web-subnet-a
Auto Assign Public IP = Enable

Security Group:-
multi-tier-sg

--------------------------------------------------------------------------------------------------------------------------------------------

Step 14: Configure Web Server

Connect:
ssh -i key.pem ec2-user@<WEB-IP>

Install Nginx:-
sudo yum install nginx -y

Start Service:-
sudo systemctl start nginx
sudo systemctl enable nginx

Website Directory:-
cd /usr/share/nginx/html

Create Frontend:-
sudo nano index.html

-----------------------------------------------------------------------------------------------------------------------------------------------

Step 15: Configure Reverse Proxy

Edit:
sudo nano /etc/nginx/nginx.conf

Add:
location ~ \.php$ {
proxy_pass http://APP-LOAD-BALANCER-DNS;
}

Validate:
sudo nginx -t

Reload:
sudo systemctl reload nginx

-------------------------------------------------------------------------------------------------------------------------------------
Step 16: Create Web AMI

Create Image:
web-ami

Disable:
Reboot Instance
Create Image.

-------------------------------------------------------------------------------------------------------------------------------------

Step 17: Create Web Auto Scaling Group

Launch Template:
web-template

AMI:
web-ami

Create:
web-asg

Subnets:
web-subnet-a
web-subnet-b

Attach Load Balancer:
Internet Facing ALB

Health Checks:
Elastic Load Balancer
Amazon EBS

Grace Period:
60 Seconds

Capacity:
Desired = 3
Minimum = 1
Maximum = 3

Enable:
Instance Scale Protection
Monitoring
Default Instance Warmup

----------------------------------------------------------------------------------------------------------------------------------------

Step 18: Access Application

Copy:
Load Balancer DNS Name

Open Browser:
http://ALB-DNS-NAME
