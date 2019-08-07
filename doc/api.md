# BISTRO API description

## user login 
**URL** /API/login.php  
**METHOD** GET  
**URL PARAMS**  
  - Required
    - username=[string]
    - password=[string]  

**SUCCESS RESPONSE**  
```json
{
"sessionID": "ev3pcioj5olqng8s865ukg5kbu",
"TTL": 1565204942
}
```json
{
"error": "You are unauthorized to make this request!"
}
```
**ERROR RESPONSE**  
**SAMPLE CALL**  
/API/login.php?username=aaa&password=aaa



## person read
**URL** /API/personread.php
**METHOD** GET  
**URL PARAMS**  
  - Required
    - sessionID=[string]
  - Optional
    - where=[string]
    - order=[string]
    - personID=[int]  

**SUCCESS RESPONSE**  
**ERROR RESPONSE**  
**SAMPLE CALL**  
/API/personread.php?sessionID=ev3pcioj5olqng8s865ukg5kbu&where=%20name%20LIKE%20%27%25pepa%25%27%20OR%20surname%20LIKE%20%27%25pepa%25%27&order=name%20asc  
/API/personread.php?sessionID=ev3pcioj5olqng8s865ukg5kbu&personID=206  

## person write
**URL** /API/personwrite.php
**METHOD** GET  
**URL PARAMS**  
  - Required
    - sessionID=[string]
**METHOD** POST  
**URL PARAMS**  
  - Required
    - 
  - Optional
    - 

**SUCCESS RESPONSE**  
**ERROR RESPONSE**  
**SAMPLE CALL**  



## user logout
**URL** /API/logout.php
**METHOD** GET  
**URL PARAMS**  
  - Required
    - sessionID=[string]  
    
**SUCCESS RESPONSE**  
**ERROR RESPONSE**  
**SAMPLE CALL**  
/API/logout.php?sessionID=jdli17sakho2g0cv0eesir026r
