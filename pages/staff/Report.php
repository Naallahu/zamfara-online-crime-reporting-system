<?php
class Report {
    private $conn;
    private $table = 'reports';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = 'INSERT INTO ' . $this->table . '
            SET
                reference_number = :reference_number,
                crime_type = :crime_type,
                description = :description,
                lga = :lga,
                location = :location,
                latitude = :latitude,
                longitude = :longitude,
                crime_date = :crime_date,
                crime_time = :crime_time,
                evidence_files = :evidence_files,
                voice_recording = :voice_recording';

        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute($data);
    }
}
