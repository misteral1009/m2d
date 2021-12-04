<?php
$date = new DateTime();
$month = $date->format("m");
$year = $date->format("Y");
if (isset($_POST['vbtn'])) {
    include('connexion.php');
    if ($_SESSION['role'] == 'admin') {
        $inf = $pdo->prepare("SELECT *, SUM(MONTANT_COTISATION) as total, adherent.sexe_adherent as classe FROM deces JOIN cotisation_deces ON deces.ID_DECES = cotisation_deces.deces JOIN adherent ON adherent.id_adherent = cotisation_deces.adherent JOIN adherent as decede ON decede.id_adherent = deces.adherent WHERE YEAR(DATE_COTISATION) = ? group by ID_DECES, adherent.sexe_adherent");
        $inf->execute([$_POST['annee']]);
    } else {
        $inf = $pdo->prepare("SELECT *, SUM(MONTANT_COTISATION) as total, adherent.sexe_adherent as classe FROM deces JOIN cotisation_deces ON deces.ID_DECES = cotisation_deces.deces JOIN adherent ON adherent.id_adherent = cotisation_deces.adherent JOIN commune ON commune.ID_COMMUNE = adherent.commune JOIN adherent as decede ON decede.id_adherent = deces.adherent WHERE YEAR(DATE_COTISATION) = ? AND adherent.commune = ? group by ID_DECES, adherent.sexe_adherent");
        $inf->execute([$_POST['annee'], $_SESSION['commune']]);
    }
    $results = $inf->fetchAll(PDO::FETCH_ASSOC);
    /* echo "<pre>";
    var_dump($results);
    echo "</pre>";
    die; */
    $defunts = [];
    $hommes = [];
    $femmes = [];
    for ($i = 0; $i < sizeof($results); $i++) {
        $temp_array = [];
        $temp_array['nom_defunt'] = $results[$i]['nom_adherent'] . " " . $results[$i]['prenom_adherent'];
        $temp_array['date_deces'] = $results[$i]['DATE_DECES'];
        if ($results[$i]['classe'] == 'M') {
            $temp_array['homme'] = $results[$i]['total'];
        } else {
            $temp_array['femme'] = $results[$i]['total'];
        }
        array_push($defunts, $temp_array);
    }
    if (isset($_POST['annee'])) {
        $etat = fopen("etat_cotisations_deces_" . $_POST['annee'] . ".xls", "w");
        fputs($etat, utf8_decode("N°\tNom du défunt\tDate de décès\tCotisation hommes\tCotisation femmes\tTotal par déces\n"));
    }
}
?>
<!DOCTYPE html>
<html lang="fr">


<!-- Mirrored from www.bootstrapdash.com/demo/skydash/template/demo/horizontal-default-light/pages/tables/data-table.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 31 Aug 2021 13:11:31 GMT -->

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>M2D</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../../vendors/feather/feather.css">
    <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="../../vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../../css/horizontal-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="../../images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>


    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">&Eacute;tat des cotisations de décès</h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <form action="" method="POST">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="">
                                                    <select class="form-select" id="annee" aria-label="Floating label select example" required name="annee">
                                                        <option selected value="">...</option>
                                                        <?php for ($i = 2021; $i <= $year; $i++) { ?>
                                                            <option value="<?= $i ?>" <?php if (isset($_POST['vbtn']) && $_POST['annee'] == $i) {
                                                                                            echo "selected";
                                                                                        } ?>><?= $i ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <label for="floatingSelect">Année<span style="color:red">*</span></label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <button class="btn btn-primary" id="vbtn" type="submit" name="vbtn">Valider</button>
                                            </div>
                                            <div class="col-4">
                                                <?php if (isset($_POST['annee'])) { ?><a class="btn btn-primary" href="./<?= "etat_cotisations_deces_" . $_POST['annee'] . ".xls" ?>">Télécharger</a><?php } ?>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                                <?php if (isset($defunts) && $defunts != array()) { ?>
                                    <div class="table-responsive" id="table">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>N°</th>
                                                    <th>Defunt</th>
                                                    <th>Date de décès</th>
                                                    <th>Cotisation des hommes</th>
                                                    <th>Cotisation des femmes</th>
                                                    <th>Total des cotisations par déces</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1;
                                                $big_t = 0;
                                                $total_hommme = 0;
                                                $total_femme = 0;
                                                foreach ($defunts as $defunt) {
                                                    fputs($etat, $i . "\t" . $defunt['nom_defunt'] . "\t" . $defunt['date_deces'] . "\t" . $defunt['homme'] . "\t" . $defunt['femme'] . "\t" . ($defunt['homme'] + $defunt['femme']) . "\n");

                                                ?>
                                                    <tr>
                                                        <td><?= $i ?></td>
                                                        <td><?= $defunt['nom_defunt'] ?></td>
                                                        <td><?= $defunt['date_deces'] ?></td>
                                                        <td><?php $total_hommme += !isset($defunt['homme']) ? 0 : $defunt['homme'];
                                                            echo !isset($defunt['homme']) ? 0 : $defunt['homme'] ?></td>
                                                        <td><?php $total_hommme += !isset($defunt['femme']) ? 0 : $defunt['femme'];
                                                            echo !isset($defunt['femme']) ? 0 : $defunt['femme'] ?></td>
                                                        <td><?php $big_t += intval(!isset($defunt['homme']) ? 0 : $defunt['homme']) + intval(!isset($defunt['femme']) ? 0 : $defunt['femme']);
                                                            echo intval(!isset($defunt['homme']) ? 0 : $defunt['homme']) + intval(!isset($defunt['femme']) ? 0 : $defunt['femme']) ?></td>
                                                    </tr>
                                                <?php $i++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <?php fputs($etat, "Total" . "\t\t\t" . $total_hommme . "\t" . $total_femme . "\t" . $big_t . "\n"); ?>
                                                    <td colspan="3" align="center">Total</td>
                                                    <td><?= $total_hommme ?></td>
                                                    <td><?= $total_femme ?></td>
                                                    <td><?= $big_t ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>

                                    </div>
                                <?php } else { ?>
                                    <div>
                                        <h3>Aucun état disponible pour cette période</h3>
                                    </div>
                                <?php }  ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
            <!-- partial -->
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="../vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <script src="../vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="../vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="../js/off-canvas.js"></script>
    <script src="../js/hoverable-collapse.js"></script>
    <script src="../js/template.js"></script>
    <script src="../js/settings.js"></script>
    <script src="../js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="../js/data-table.js"></script>
    <!-- End custom js for this page-->
    <script>
        function print() {
            preventDefault();
            window.print();

        }
        /* var mois = document.getElementById('mois')
        var annee = document.getElementById('annee')
        var vbtn = document.getElementById('body')
        var data;
        var set = {
            mois: null,
            annee: null
        }
        annee.addEventListener('change', function(e) {
            set.annee = annee.value
            ennabledButton()
        })
        vbtn.addEventListener('click', function() {
            const data = {
                'method': 'getEtatCotisationMensuelle',
                'annee': set.annee,
            }
            fetch('function.php', {
                    method: 'POST',
                    mode: 'cors',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    console.log(response)
                })
                .then(result => {
                    console.log(result);
                    //fillTable(result)
                })
                .catch(error => {
                    console.error('Error', error)
                })
        })

        function ennabledButton() {
            if ((set.mois !== null && set.annee !== null) && (set.mois !== "" && set.annee !== "")) {
                document.getElementById("vbtn").classList.remove('disabled')
            } else {
                document.getElementById("vbtn").classList.add('disabled')
            }
        }

        function fillTable(param) {
            var content = "";
            for (obj in param) {
                content += `
                
                                <details>
                                    <summary>
                                        ${param.mois}
                                    </summary>
                                </details>
                                <div class="table-responsive mt-3">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>N°</th>
                                                <th>Nom et Prénom</th>
                                                <th>Sexe</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>Commune</th>
                                                <th>Montant</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table_body">
                                            ${content}
                                        </tbody>
                                    </table>
                                </div>`
            }

            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }
            tbody.innerHTML = content
        } */
    </script>
</body>
<!-- Mirrored from www.bootstrapdash.com/demo/skydash/template/demo/horizontal-default-light/pages/tables/data-table.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 31 Aug 2021 13:11:32 GMT -->

</html>