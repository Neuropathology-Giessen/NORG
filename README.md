# NORG – Nitrogen ORGanizer: A standalone database system for local preservation of cryomaterial 
<img align="right" width="200" height="200" src="norg.png">

The NORG System is a cutting-edge solution designed to revolutionize the way biological samples are managed and preserved. This repository serves as the official documentation for the NORG System, providing a comprehensive guide to its features, functionalities, and implementation.

If you wish, you can try NORG at: https://gin-norg-dev.med.uni-giessen.de/


### Overview

The NORG System addresses a critical challenge in the field of sample preservation – the efficient management of diverse biological samples, ranging from human specimens to plant materials. The preservation process typically involves packaging samples into appropriate containers and storing them in various environments, such as cryogenic containers, freezers, and more. To maximize the value of a biological sample database, meticulous cataloging and continuous maintenance are essential. Without a clear understanding of the sample locations, the database loses its significance.

In many instances, sample documentation is carried out using local spreadsheets saved in formats like CSV or XLSX. In some cases, even handwritten records are utilized to track sample locations. The primary purpose of a biological database is to collect and store rare materials while facilitating sample withdrawal for further analysis, both internally and in collaboration with external partners.

A recurring issue arises when samples are withdrawn from containers that are already fully stored. Reallocating empty spaces within containers demands extensive administrative efforts. Without efficient management, new samples might unintentionally occupy containers that are currently active, leading to inefficiencies and potential data loss.

### Introducing NORG

The NORG System offers a comprehensive and user-friendly software solution through web services, revolutionizing the way biological samples are managed. Key features include:

#### Sample Cataloging: 
NORG provides a seamless platform to digitally catalog various sample types, ensuring accurate record-keeping and easy retrieval.
#### Color-Coded Filling Levels: 
To simplify the monitoring of storage conditions, NORG employs color-coding to indicate the filling levels of storage units, such as nitrogen tanks. This visual representation enhances efficient sample management.
#### Traceability: 
NORG enables transparent tracking of sample movement. Users can easily identify the origins and destinations of samples, streamlining collaboration with external partners and analytical processes.
#### Cooperation Tracking: 
Collaborative projects become more manageable with NORG's ability to trace sample transfers to and from external partners. This feature facilitates analysis by ensuring seamless communication and tracking.
Getting Started

### Contact
For inquiries, support, and collaboration opportunities, please contact daniel.amsel@patho.med.uni-giessen.de .

Thank you for considering the NORG System as your next-generation solution for biological sample management. Together, we can revolutionize the way we preserve and utilize valuable biological resources.

### Required Software
- Docker (27.0.3)
- Docker-compose (v2.6.1)

  
### How to install 
The installation is easily manageable via Docker containerization and Docker-compose.

```bash

mkdir /mariadb_data/
sudo chown -R 1001:1001 /mariadb_data/
sudo chmod -R 775 /mariadb_data/

git clone https://github.com/DanielAmsel/GIN-NORG.git

cd GIN-NORG

docker-compose up
```
This should pull all the necessary containers.
If this is your first start, your need to initialize the database by using these four commands in a separate console:

```bash
docker exec norg_laravel php artisan key:generate

docker exec norg_laravel php artisan migrate

docker exec norg_laravel php artisan db:seed

docker exec norg_laravel php artisan route:cache

docker exec norg_laravel composer update
```


You can now login via the default credentials
`admin@norg.de`
`adminpass`
*please change the credentials as soon as you can*

It is highly recommendable to do system backups of the data you entered into your database. Instead of programming a complex backup solution, we rely on commonly used and well-known cron-jobs that run an export script of the database as often as you like.

### FHIR Integration
FHIR (Fast Healthcare Interoperability Resources) capabilities are integrated into the NORG System, improving the managment and sharing of biological samples. Making it easier to share data across healthcare systems. 
Using the FHIR Location resource is an important part of NORG's FHIR integration. This resource is used to represent the physical locations where samples are stored, as well as the sample's status, the identifiers, the institution's address and further information.

#### Sample Management:
Manage samples, including creation, shipping, re-storage, and deletion, all within the FHIR framework.

#### Configurable Settings: 
Users can enable or disable FHIR capabilities within the config/fhir.php file according on their needs, providing flexibility in the management and sharing of data.

#### Institution Address:
To ensure correct representation in the FHIR ecosystem, users can additionally set their institute's address in the config/fhir.php file.

#### Docker Integration:
The NORG System setup includes Docker Compose configuration that creates an FHIR server, resulting in a ready-to-use environment for FHIR-related tasks.

### Database Backup and Recovery
To ensure the integrity and security of your data within the NORG system, we implemented a solution for automated backups of the entire database. We use scheduled backups to make sure you won't lose your data. Here's how you can set it up and recover data if needed.

**Automating Backup Routine with Crontab**
To automate the backup process, a crontab job can be set up on your server. You have two main options based on your needs.

1. **For Full Backup with Script:** if you prefer a comprehensive backup that includes the SQL dump and the CSV tables, you can schedule the execution of the db_export.sh script. 

    **Prepare the Backup Script:** First, ensure your backup script is ready and executable by changing its permissions:
    ```bash
    chmod +x db_export.sh
    ```
    **Transfer the Script to the Database Container:** Use Docker to copy the script into your MariaDB container:
    ```bash
    docker cp db_export.sh [mariadb container name]:/tmp/db_export.sh
    ```
    **Execute the Backup Script:** Run the script within the container to perform the backup:
    ```bash
    docker exec -i [mariadb container name] bash -c "/tmp/db_export.sh"
    ```
    Before you make the `db_export.sh` script executable, you should first update it with your actual database credentials. Replace the placeholders with your actual values:
    ```bash
    USERNAME="your_username"
    PASSWORD="your_password"
    DATABASE_NAME="your_database_name"
    EXPORT_BASE_PATH="/dumps"
    ```


    Here's how to set up a crontab job to run this script daily at midnight:
    ```bash
    0 0 * * * docker exec -i [mariadb container name] bash -c "/tmp/db_export.sh
    ```
    Make sure to replace [mariadb container name] with the actual name of your MariaDB container. 

2. **For SQL Dump Only:** If you only need an SQL dump without the extra steps provided by the db_export.sh script, you can use a more straightforward crontab job. This approach does not require making any script executable. Simply use this command for a daily backup routine to occur at midnight:
    ```bash
    0 0 * * * docker exec -i [mariadb container name] mysqldump -u root --password=your_password your_database_name > /opt/GIN-NORG/public/sqldumps/NorgDBdump$(date +%Y%m%d).sql
    ```
    Again replace `[mariadb container name]`, `your_password` and `your_database_name`.

**Data Recovery Process:**

Restoring your database from a backup is straightforward. Follow these steps to use your SQL backup file for recovery:

Place the SQL Backup File: First, ensure the SQL backup file is located within an accessible directory for the Docker Compose environment. For example store it under public/sqldumps/. 

Execute the Restore Command: Use the following command to restore your database from the chosen backup file. Replace [mariadb container name], your_password, your_database_name, and [date] with the appropriate values for your setup:
```bash
docker exec -i [mariadb container name] mysql -u root --password=your_password your_database_name < public/sqldumps/NorgDBdump[date].sql
```

## How to use

### Importing Samples from CSV
If you're moving to NORG from another system and have your sample data stored in a CSV file, NORG simplifies the process of importing this data directly through its web application. Here’s how to bring your existing sample data into NORG:

1. **Prepare Your CSV File:** Ensure your CSV file is formatted correctly according to NORG's requirements. Each entry should include the necessary information for a single sample: a storage location (with tank name, position insert, position tube, and position sample), a sample identifier, a type of material, a responsible person, and a storage date.

2. **Log In to NORG:** Use your credentials to log in to the NORG web application.

3. **Prepare NORG for Import:** This preparation involves setting up certain elements within the NORG system that correspond to the information in your CSV file. If the responsible persons, material types or the tanks from your CSV does not exist in NORG, you'll need to add them to the system.

4. **Navigate to the Import Section and Upload Your CSV File:** Find the "CSV Import" option within the NORG dashboard and upload your CSV file. 

After the import process is completed, you will receive a notification within the NORG system indicating whether the import was successful.

This is how the csv should look like:
| #  | tank name | container | tube | position | ID            | material | responsible person           | storage date  |
|----|-----------|-----------|------|----------|---------------|----------|------------------------------|---------------|
| 1  | 6         | 1         | 1    | 1        | B846-22_ISS   | TM       | testuser@testmail.com | 20.07.2022 09:13 |
| 2  | 6         | 1         | 1    | 2        | B855-22_ISS   | TM       | testuser@testmail.com | 20.07.2022 09:14 |
| 3  | 6         | 1         | 1    | 5        | B862-22_laSS  | TM       | testuser@testmail.com | 20.07.2022 09:16 |
| 4  | 6         | 1         | 2    | 1        | B871-22_laSS  | TM       | testuser@testmail.com | 20.07.2022 09:17 |


### Additional Guidance

For a detailed explanation of NORG’s functionalities and step-by-step instructions, including sample importation, check out our paper, "NORG - Nitrogen ORGanizer: a novel web-based system for cryopreservation and freezer management." This publication offers a comprehensive insights into leveraging NORG for biological sample management.

## License
GPL-3.0 license 

## How to cite
Keller, M., Przybilla, M., Sehring, J., Schmid, K., Kauer, T., Dörmer, F., ... & Amsel, D. (2026). NORG-Nitrogen ORGanizer: a novel web-based system for cryopreservation and freezer management. Database, 2026, baag037.


