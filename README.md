# Fonbet Parser

A Symfony + React project for parsing esports matches (CS2 and Dota2) from Fonbet and other sources (planned for future support).
Supports Docker, frontend build, and cron tasks.

Technologies:
* Database: MySQL 8.0
* Backend: PHP 8.4 + Symfony
* Frontend: React + CSS
* Environment: Docker

---

## 🚀 Installation

1. Clone the repository:

```bash
git clone <repo_url>
cd fonbet-parser

```

2. Configure .env with database settings: 
```dotenv
    #.env - EXAMPLE
    APP_ENV=dev
    APP_SECRET=0f5b5368b6a8d875a87d4d2ac26f204c
    DATABASE_URL="mysql://fonbet:fonbet@db:3306/fonbet"
```
3. Configure cron in docker/php/cronjob
```
    # Example: Every Minute
    * * * * * /usr/local/bin/php /var/www/bin/console app:parse-matches --source=Fonbet --days=1 >> /var/www/var/log/parser.log 2>&1
```

Available options for parse-matches:

| Option         | Description                                   | Default  |
| -------------- | --------------------------------------------- | -------- |
| `--source`     | Data source                                   | `Fonbet` |
| `--days`       | Number of days to parse                       | `1`      |
| `--tournament` | Tournament name (optional)                    | `null`   |
| `--team`       | Team name (optional)                          | `null`   |
| `--status`     | Match status (`all`, `completed`, `canceled`) | `all`    |


4. Build and start Docker containers
```
    docker-compose build
    docker-compose up -d
```


5. Install PHP and Node dependencies

```
    docker-compose run --rm php composer install
    docker-compose run --rm node npm install
    docker-compose up -d
```

5. Apply migrations
```
    docker-compose exec php php bin/console doctrine:migrations:migrate

```
⚠️ Warning: This may change the database schema and cause data loss. Confirm with yes.


7. Run parsing manually
``` 
    docker-compose exec php php bin/console app:parse-matches --source=Fonbet --days=1
```

6. Build the frontend
   
```
    docker-compose run --rm node npm run build
```
Frontend URL: http://localhost:8000/home

---

## 🛠 Additional Features:
Interactive Parsing
``` 
     docker-compose exec php php bin/console app:parse-matches-interactive 
```

Example workflow:
```
time="2025-08-20T06:36:11+02:00" level=warning msg="C:\\Users\\komis\\Desktop\\fonbet-parser\\docker-compose.yaml: the attribute `version` is obsolete, it will be ignored, please remove it to avoid potential confusion"
Select data source (default: Fonbet)
[0] Fonbet
>
Source: Fonbet
Enter number of days to parse [default 1]:
Enter tournament name (optional, press Enter to skip):
Enter team name (optional, press Enter to skip):
Select match status (default: All matches)
[0] All matches
[1] Completed matches
[2] Canceled matches
>
Status: All matches
Enter repeat interval in hours (optional, press Enter to skip):
=== Start parsing matches ===
Data successfully downloaded from Fonbet
Successfully founded Esport category ID
Successfully filtered competitions
Successfully filtered events
=== End parsing matches ===
=== Start saving matches to local DB ===
Successfully processed event 57542574
....
Successfully processed event 57542709
=== End saving matches to local DB ===
Parsing completed successfully!  
```

---

## 📦 Database Structure


### Matches:

| Field               | Type                   | Description                  |
| ------------------- | ---------------------- |------------------------------|
| `id`                | int                    | Primary key                  |
| `source_id`         | string                 | External match ID            |
| `discipline`        | string                 | Game discipline (CS2/Dota2)  |
| `tournament`        | Tournaments            | Related tournament           |
| `match_format`      | string                 | Format of match (f.ex.: bo3) |
| `score1`            | int                    | Team 1 score                 |
| `score2`            | int                    | Team 2 score                 |
| `team1`             | Teams                  | Team 1 entity                |
| `team2`             | Teams                  | Team 2 entity                |
| `status`            | MatchStatus            | Match status                 |
| `submatches_number` | int                    | Number of submatches         |
| `subMatches`        | Collection<SubMatches> | Related submatches           |



### Tournaments

| Field  | Type   | Description     |
| ------ | ------ | --------------- |
| `id`   | int    | Primary key     |
| `name` | string | Tournament name |


### Teams

| Field  | Type   | Description |
| ------ | ------ | ----------- |
| `id`   | int    | Primary key |
| `name` | string | Team name   |


### Submatches

| Field       | Type    | Description          |
| ----------- | ------- | -------------------- |
| `id`        | int     | Primary key          |
| `source_id` | string  | External submatch ID |
| `score1`    | int     | Team 1 score         |
| `score2`    | int     | Team 2 score         |
| `title`     | string  | Submatch title       |
| `match`     | Matches | Related match        |


## SCREENSHOTS

1. Main Page

2. Logs Page






