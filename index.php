<?php 
include 'mysqli.php';
if(isset($_GET['sid']) and is_numeric($_GET['sid'])) {
    $id = $_GET['sid'];
    $sql = 'DELETE FROM simple_small WHERE id = '.$id;
    if($kl->dbQuery($sql)) {
        header('Location: index.php');
    } else {
        ?>
        <p class="text-danger fw-bold">Midagi läks kustutamisega valesti</p>
        <?php
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.1.1/css/all.min.css">
    <title>CRUD</title>
</head>

<body>
    <div class="container">
        <div class="row my-2">
            <a href="create.php" class="btn btn-success col-2">Lisamine</a>
            <a href="cards.php" class="btn btn-warning col-2 mx-2">Cards view</a>
        </div>
        <!-- TABELI OSA -->
        <div class="row">
            <?php
            // TOtal ja Keskmine
            $sql ='SELECT SUM(salary) AS total, AVG(height) AS average FROM simple_small';
            $n_res = $kl->dbGetArray($sql);
            if($n_res !== false) {
                $n_res = $n_res[0]; //$n_res['total'] vs $n_res['average']
            }

            // Kõik kirjed
            $sql = 'SELECT * FROM simple_small ORDER BY birth DESC';
            $res = $kl->dbGetArray($sql);
            if ($res !== false) {
            ?>
                <table class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>Jrk</th>
                            <th>Nimi</th>
                            <th>Sünniaeg</th>
                            <th>Palk</th>
                            <th>Pikkus</th>
                            <th>Lisatud</th>
                            <th>Tegevus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $salary_total = 0;
                        $height_total = 0;
                        foreach ($res as $key => $val) {
                            $salary_total +=$val['salary']; // iga loopiga kasvab
                            $height_total +=$val['height']; // hiljem on count($res)
                        ?>
                            <tr>
                                <td class="text-end"><?php echo $key+1; ?>.</td>
                                <td><?php echo $val['name']; ?></td>
                                <td class="text-center"><?php echo $kl->dbDateToEstDate($val['birth']); ?></td>
                                <td class="text-end"><?php echo $val['salary']; ?></td>
                                <td class="text-end"><?php echo number_format($val['height'],2,',',''); ?></td>
                                <td class="text-end"><?php echo $kl->dbDateToEstDateClock($val['added']); ?></td> 
                                <td class="text-center">
                                    <span><?php echo $val['id']; ?></span>
                                    <a href="update.php?sid=<?php echo $val['id']; ?>"><i class="col-6 fa-solid fa-rotate-right"></i></a>
                                    <a href="index.php?sid=<?php echo $val['id']; ?>" onclick="return confirm('Kas kustutame kirje jäädavalt?')"><i class="fa-solid fa-trash text-danger"></i></a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end">Ise arvutatud</td>
                            <td class="text-end"><?php echo $salary_total; ?></td>
                            <td class="text-end"><?php echo number_format($height_total / count($res), 3,",", "")?></td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">SQL-i arvutus</td>
                            <td class="text-end"><?php echo $n_res['total']; ?> </td>
                            <td class="text-end"><?php echo number_format($n_res['average'], 3,",", "");?></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            <?php
            }
            ?>
        </div>
        
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>