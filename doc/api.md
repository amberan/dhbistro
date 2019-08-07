# BISTRO API description

## user login 
**URL** /API/login.php  
**METHOD** GET  
**URL PARAMS**  
  - Required
    - username=[string]
    - password=[string]  
**SUCCESS RESPONSE**  
**ERROR RESPONSE**  
**SAMPLE CALL**  
/API/login.php?username=aaa&password=aaa



## read person
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
/API/personread.php?sessionID=ev3pcioj5olqng8s865ukg5kbu&where=power%3D1&order=name%20asc  
/API/personread.php?sessionID=ev3pcioj5olqng8s865ukg5kbu&personID=206  



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
