# Casualty dataset 

A small provided dataset (mysqldump) for the Vietnam and Korean war casualties on American side. 

## Installation 

### From scratch (Using the source dataset):

1. Install the database with the follwoing command: `mysql -u {user} -p {database} < {path to scheme}`
2. Run the following queries: 
 - `LOAD DATA LOCAL INFILE 'sources/DCAS.VN.EXT08.DAT' INTO TABLE Vietnam_casualties FIELDS TERMINATED BY '|';`
 - `LOAD DATA LOCAL INFILE 'sources/DCAS.KS.EXT08.DAT' INTO TABLE Korean_casualties FIELDS TERMINATED BY '|';`

## Sources 

- [The vietnam war.](https://catalog.archives.gov/id/2240992)
- [The korean war.](https://catalog.archives.gov/id/2240988)