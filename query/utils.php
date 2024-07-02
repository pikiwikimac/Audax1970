<?php

    // Tutti i dirigenti

    function getDirigente1($con) {
        $query = "
        select *
        FROM dirigenti
        where ruolo='dirigente1'
        ";

        $result = mysqli_query($con, $query);
        return $result;
    }

     // Tutti i dirigenti
     function getDirigente2($con) {
        $query = "
        select *
        FROM dirigenti
        where ruolo='dirigente2'
        ";

        $result = mysqli_query($con, $query);
        return $result;
    }

     // Tutti i dirigenti
     function getDirigente3($con) {
        $query = "
        select *
        FROM dirigenti
        where ruolo='dirigente3'
        ";

        $result = mysqli_query($con, $query);
        return $result;
    }

     // Tutti i dirigenti
     function getDirigente4($con) {
        $query = "
        select *
        FROM dirigenti
        where ruolo='dirigente4'
        ";

        $result = mysqli_query($con, $query);
        return $result;
    }

    // Allenatore
    function getAllenatore($con) {
        $query = "
        select *
        FROM dirigenti
        where ruolo='Allenatore'
        ";

        $result = mysqli_query($con, $query);
        return $result;
    }

    // Capitano
    function getCapitano($con,$data) {
        $query = "
        SELECT *
        FROM `giocatori` g
        WHERE capitano = 'C'
        and  g.id not in ( select i.id
                            FROM indisponibili i
                            where da_data <= $data
                            and a_data >= $data)
        ";
        $result = mysqli_query($con, $query);
        return $result;
    }

     // Vicecapitano
     function getViceCapitano($con,$data) {
        $query = "
        SELECT *
        FROM `giocatori` g
        WHERE capitano = 'VC'
        and  g.id not in ( select i.id
                            FROM indisponibili i
                            where da_data <= $data
                            and a_data >= $data)
        ";

        $result = mysqli_query($con, $query);
        return $result;
    }

    function getSocieta($con,$data) {
        $query = "
        SELECT *
        FROM `societa` s
        WHERE s.id= $data
        ";

        $result = mysqli_query($con, $query);
        return $result;
    }

?>