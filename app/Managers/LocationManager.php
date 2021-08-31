<?php
namespace App\Managers; 
class LocationManager {
    public static function getNearestLocation($lat,$lng){
        return app('db')->select(
            "SELECT id, zipcode, name_en, name_de, region, language, elevation, X(position) as lng, Y(position) as lat,
                (
                GLength(
                    LineStringFromWKB(
                    LineString(
                        position, 
                        ST_GeomFromText('POINT($lat $lng)')
                    )
                    )
                )
                )
                AS distance
            FROM locations
                ORDER BY distance ASC
                LIMIT 10");
    
    }
}
?>


