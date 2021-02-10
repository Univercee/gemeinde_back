

USE location_service;

-- get services by zipcode

SELECT * FROM services
JOIN location_services
ON location_services.service_id = services.id
WHERE location_services.location_zipcode = "1219"
                    
				