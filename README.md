AWS Multi-Tier Architecture Deployment Steps
# This project deploys a three-tier architecture consisting of:

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

Step 3: Create Route Tables

Create:
public-rt
private-rt

Associate:
public-subnet-a
public-subnet-b

with:
public-rt

Step 4: Create Internet Gateway

Create:
multi-tier-igw

Attach to:
multi-tier-vpc

Add Route:
0.0.0.0/0

Target:
Internet Gateway

Step 5: Create RDS Subnet Group

Create:
db-subnet-group

Select:
db-subnet-a
db-subnet-b

Availability Zones:
ap-south-1a
ap-south-1b

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

Step 8: Connect Database

Install MySQL Client:-
sudo yum install mariadb105-server -y

Connect to RDS:-
mysql -u root -p -h <RDS-ENDPOINT>

Create Database:-
CREATE DATABASE multitier;
USE multitier;

Step 9: Create Application Server

Launch EC2:
Name = app-instance
VPC = multi-tier-vpc
Subnet = app-subnet-a
Auto Assign Public IP = Enable

Use existing:
multi-tier-sg

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

Step 11: Create App AMI

Create Image:
AMI Name = app-ami

Disable:
Reboot Instance

Create Image.

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

Step 13: Create Web Server

Launch EC2:
Name = web-server
VPC = multi-tier-vpc
Subnet = web-subnet-a
Auto Assign Public IP = Enable

Security Group:-
multi-tier-sg

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
Step 16: Create Web AMI

Create Image:
web-ami

Disable:
Reboot Instance
Create Image.

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


Step 18: Access Application

Copy:
Load Balancer DNS Name

Open Browser:
http://ALB-DNS-NAME
