<?php
include 'mysqli.php';
if(isset($_GET['sid']) and is_numeric($_GET['sid'])) {
    $id = $_GET['sid'];
    $sql = 'DELETE FROM simple_small WHERE id = '.$id;
    if($kl->dbQuery($sql)) {
        header('Location: cards.php');
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
    <title>Cards</title>
</head>

<body>

    <?php
    $sql = 'SELECT * FROM simple_small';
    $res = $kl->dbGetArray($sql);
    if ($res !== false) {
    ?>

        <div class="container-fluid">
            <div class="col-3 my-2" style="max-width: 12rem;">
                <a href="index.php" class="form-control btn btn-warning">Avaleht</a>
            </div>
            <div class="row row-cols-auto mx-auto">
                <?php
                foreach ($res as $key => $val) {
                ?>
                    <div class="col-sm-auto col-md-auto col-lg-auto g-1">
                        <div class="card" style="max-width: 20rem;">
                            <div class="card-header text-center bg-secondary text-white"><?php echo $val['name']; ?></div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="card-text">Sünniaeg: </td>
                                        <td class="card-text text-start"><?php echo $kl->dbDateToEstDate($val['birth']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="card-text">Palk: </td>
                                        <td class="card-text text-start"><?php echo $val['salary']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="card-text">Palk: </td>
                                        <td class="card-text text-start"><?php echo $val['height']; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="card-footer row g-0 row-cols-2 bg-secondary text-white">
                                <div class="col">
                                    <div class="text-start">
                                        <a class="btn text-white fa-solid fa-trash" href="cards.php?sid=<?php echo $val['id']; ?>"onclick="return confirm('Kas kustutame kirje jäädavalt?')"></a>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="text-end">
                                        <p><?php echo $kl->dbDateToEstDateClock($val['added']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>        
                <?php
                }
                ?>
            </div>
        </div>
    <?php
    }
    ?>




</body>

</html>