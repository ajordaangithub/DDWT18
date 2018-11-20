<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

        <!-- Own CSS -->
        <link rel="stylesheet" href="/DDWT18/week1/css/main.css">

        <title><?= $page_title ?></title>
    </head>
    <body>
        <!-- Menu -->
        <?= $navigation ?>

        <!-- Content -->
        <div class="container">
            <!-- Breadcrumbs -->
            <div class="pd-15">&nbsp</div>
            <?= $breadcrumbs ?>

            <div class="row">

                <!-- Left column -->
                <div class="col-md-8">
                    <!-- Error message -->
                    <?php if (isset($error_msg)){echo $error_msg;} ?>

                    <h1><?= $page_title ?></h1>
                    <h5><?= $page_subtitle ?></h5>
                    <p><?= $page_content ?></p>
                    <form method="POST" action=<?= $form_action ?> >
                    <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-formlabel">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputName" value="<?php if (isset($series_info)){echo $series_info['name'];} ?>"
                                   name="Name" required >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputCreator" class="col-sm-2 col-formlabel">Creator</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputCreator"
                                   name="Creator" required value="<?php if (isset($series_info)){echo $series_info['creator'];} ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputSeasons" class="col-sm-2 col-formlabel">Seasons</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="inputSeasons"
                                   name="Seasons" required value="<?php if (isset($series_info)){echo $series_info['seasons'];} ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputAbstract" class="col-sm-2 col-formlabel">Abstract</label>
                        <div class="col-sm-10">
                            <textarea rows="4" cols="40" class="form-control" id="inputAbstract" name="Abstract" required><?php if (isset($series_info)){echo $series_info['abstract'];} ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary"> <?=$submit_btn ?></button>
                        </div>
                    </div>
                    <!-- adds hidden form with serie id when a serie is edited -->
                    <?php if (count($_GET) > 0) {
                        echo "
                        <div class=\"form-group row\">
                            <label for=\"inputAbstract\" class=\"col-sm-2 col-formlabel\"></label>
                            <div class=\"col-sm-10\">
                            <input type=\"hidden\" class=\"form-control\" id=\"series_id\" name=\"serie_id\" value='$serie_id'>
                             </div>
                        </div>";
                        }
                        ?>




                    </form>
                </div>

                <!-- Right column -->
                <div class="col-md-4">

                    <?php include $right_column ?>

                </div>

            </div>
        </div>


        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>
</html>