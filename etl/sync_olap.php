<?php

/* OLTP CONNECTION*/

$oltp = mysqli_connect(
    "localhost",
    "root",
    "",
    "donation_oltp"
);

/* OLAP CONNECTION*/

$olap = mysqli_connect(
    "localhost",
    "root",
    "",
    "donation_olap"
);

if(!$oltp || !$olap){

    die("Database Connection Failed");

}

/* CLEAR OLAP TABLES*/

mysqli_query($olap,"SET FOREIGN_KEY_CHECKS=0");

mysqli_query($olap,"TRUNCATE TABLE fact_donations");
mysqli_query($olap,"TRUNCATE TABLE dim_donor");
mysqli_query($olap,"TRUNCATE TABLE dim_time");
mysqli_query($olap,"TRUNCATE TABLE dim_location");
mysqli_query($olap,"TRUNCATE TABLE dim_payment");

mysqli_query($olap,"SET FOREIGN_KEY_CHECKS=1");

/* EXTRACT DATA FROM OLTP*/

$sql = "
SELECT d.donation_id, d.donor_id, d.amount, d.payment_method, d.donation_date, d.status,
       dn.first_name, dn.last_name, dn.city, dn.province, dn.region
FROM donations d
INNER JOIN donors dn ON d.donor_id = dn.donor_id
WHERE d.status = 'Completed'
";

$result = mysqli_query($oltp,$sql);

$total_records = 0;

/* ETL PROCESS*/

while($row = mysqli_fetch_assoc($result))
{

    /* DONOR DIMENSION*/

    $donor_name =
    $row['first_name'] .
    ' ' .
    $row['last_name'];

    mysqli_query(
        $olap,
        "
        INSERT INTO dim_donor
        (
            donor_id,
            donor_name
        )
        VALUES
        (
            {$row['donor_id']},
            '$donor_name'
        )
        "
    );

    $donor_key =
    mysqli_insert_id($olap);

    /*TIME DIMENSION*/

    $date = $row['donation_date'];

    $day =
    date(
        'j',
        strtotime($date)
    );

    $month =
    date(
        'n',
        strtotime($date)
    );

    $month_name =
    date(
        'F',
        strtotime($date)
    );

    $quarter =
    ceil($month / 3);

    $year =
    date(
        'Y',
        strtotime($date)
    );

    mysqli_query(
        $olap,
        "
        INSERT INTO dim_time
        (
            full_date,
            day_num,
            month_num,
            month_name,
            quarter_num,
            year_num
        )
        VALUES
        (
            '$date',
            $day,
            $month,
            '$month_name',
            $quarter,
            $year
        )
        "
    );

    $time_key =
    mysqli_insert_id($olap);

    /*LOCATION DIMENSION*/

    mysqli_query(
        $olap,
        "
        INSERT INTO dim_location
        (
            city,
            province,
            region
        )
        VALUES
        (
            '{$row['city']}',
            '{$row['province']}',
            '{$row['region']}'
        )
        "
    );

    $location_key =
    mysqli_insert_id($olap);

    /*PAYMENT DIMENSION*/

    mysqli_query(
        $olap,
        "
        INSERT INTO dim_payment
        (
            payment_method
        )
        VALUES
        (
            '{$row['payment_method']}'
        )
        "
    );

    $payment_key =
    mysqli_insert_id($olap);

    /*FACT TABLE*/

    mysqli_query(
        $olap,
        "
        INSERT INTO fact_donations
        (
            donor_key,
            time_key,
            location_key,
            payment_key,
            donation_amount
        )
        VALUES
        (
            $donor_key,
            $time_key,
            $location_key,
            $payment_key,
            {$row['amount']}
        )
        "
    );

    $total_records++;
}

/* SUCCESS PAGE*/
?>

<!DOCTYPE html>
<html>
<head>

<title>ETL Sync Complete</title>

<style>

body{

    font-family:Arial;
    text-align:center;
    padding:50px;
}

.success{

    width:600px;

    margin:auto;

    padding:30px;

    background:#e8f5e9;

    border:1px solid #4caf50;

    border-radius:10px;
}

a{

    text-decoration:none;
}

button{

    padding:10px 20px;
    cursor:pointer;
}

</style>

</head>

<body>

<div class="success">

<h1>
Data Analyzation Complete!
</h1>

<p>

Successfully loaded

<strong>

<?php echo $total_records; ?>

</strong>

completed donation records.

</p>

<br>

<a href="../dashboard/dashboard.php">

<button>

Back to Dashboard

</button>

</a>

</div>

</body>
</html>