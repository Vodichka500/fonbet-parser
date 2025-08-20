

cd project_name
docker-compose run --rm php composer install
docker-compose run --rm node npm install
docker-compose up -d
docker-compose exec php php bin/console doctrine:migrations:migrate

PS C:\Users\komis\Desktop\fonbet-parser> docker-compose exec php php bin/console app:parse-matches-interactive      
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
Successfully processed event 57542572
Successfully processed event 57542571
Successfully processed event 57542570
Successfully processed event 57553045
Successfully processed event 57542569
Successfully processed event 57542568
Successfully processed event 57542567
Successfully processed event 57542566
Successfully processed event 57542565
Successfully processed event 57528031
Successfully processed event 57528035
Successfully processed event 57527868
Successfully processed event 57542564
Successfully processed event 57542563
Successfully processed event 57542716
Successfully processed event 57542715
Successfully processed event 57542714
Successfully processed event 57542713
Successfully processed event 57542711
Successfully processed event 57542712
Successfully processed event 57542710
Successfully processed event 57542709
=== End saving matches to local DB ===
Parsing completed successfully!


visit http://localhost:8080/home 

for run crone process


