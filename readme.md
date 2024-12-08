# InfoBridgeMax

InfoBridgeMax is a high-performance database management system leveraging **Redis** to manage and query real-time information about employees, departments, and projects. This application offers enhanced speed and scalability for data management within an organization.

## Prerequisites

To run this application, ensure you have the following installed:
- **Redis** (for in-memory database storage)
- **PHP** (for server-side scripting)
- **Docker** and **Docker Compose** (for containerized setup)
- **Linux Terminal** with sudo privileges (to manage containers and interact with Redis)

## Setup Instructions

### Step 1: Build and Start PHP and MySQL Web Application Docker Container

1. Navigate to the directory containing your `docker-compose.yml` file.
2. Run the following command to build and start the container:
   ```bash
   docker-compose up
3. Open localhost in your web browser
   
### Step 2: To access and load files in MySQL
1. Check the containers:
   ```bash
   docker ps -a
2. Check the Images:
   ```bash
   docker images
3. To Load the ".dat" files Open a Web Browser and type this in the web browser
   ```bash
   localhost/load_redis.php
4. Run the following command in a new terminal of the same directory to access Redis Container:
   ```bash
   docker exec -it <redis_container_id> redis-cli
5. After opening the cli you can the hashes and its fields with values
   ```bash
   HGETALL DEPARTMENT:1
6. To Open the Web Application type this in Web Browser
   ```bash
   localhost
### Step 3: To stop the containers

1. Use this command to stop containers
   ```bash
   docker-compose down
2. To remove the image in docker
   ```bash
   docker rmi <image_name>
