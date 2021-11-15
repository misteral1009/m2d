<?php
include('connexion.php');
$inf = $pdo->query("SELECT * FROM deces JOIN adherent ON id_adherent = deces.adherent ");
$results = $inf->fetchAll();
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
            <h4 class="card-title">Cotisations de décès</h4>
            <div class="row">
              <div class="col-12">
                <div class="row">
                  <div class="row">
                    <div class="col-4">
                      <div>
                        <select class="form-select" id="deces" aria-label="Floating label select example">
                          <option selected value="">...</option>
                          <?php foreach ($results as $deces) { ?>
                            <option value="<?= $deces['ID_DECES'] ?>"><?= $deces['nom_adherent']." ".$deces['prenom_adherent']?></option>
                          <?php } ?>
                        </select>
                        <label for="floatingSelect">Décès<span style="color:red">*</span></label>
                      </div>
                    </div>
                    <div class="col">
                      <button class="btn btn-primary disabled" id="vbtn">Valider</button>
                      <button class="btn btn-primary disabled" id="btn">Impriper</button>
                    </div>
                  </div>
                </div>
                <div class="table-responsive mt-3">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>N°</th>
                        <th>Nom et Prénom</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Commune</th>
                        <th>Montant</th>
                      </tr>
                    </thead>
                    <tbody id="table_body">
                      <tr>
                        <td>1</td>
                        <td>2012/08/03</td>
                        <td>Edinburgh</td>
                        <td>New York</td>
                        <td>$1500</td>
                        <td>$3200</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
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
    var deces = document.getElementById('deces')
    var vbtn = document.getElementById('vbtn')
    var tbody = document.getElementById('table_body')
    var tbody_content = document.getElementById('tbody_content')
    var data;
    var deces_id;
    deces.addEventListener('change', function(e) {
      deces_id = deces.value
      ennabledButton()
    })
    vbtn.addEventListener('click', function() {
      const data = {
        'method': 'getCotisationDeces',
        'deces': deces_id
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
          fillTable(result)
        })
        .catch(error => {
          console.error('Error', error)
        })
    })

    function ennabledButton() {
      if (deces.value !== null && deces.value !== '') {
        document.getElementById("vbtn").classList.remove('disabled')
      } else {
        document.getElementById("vbtn").classList.add('disabled')
      }
    }

    function fillTable(param) {
      var content = "";
      var i = 1;
      if (param == null) {
        content += `
          <tr>
            <td colspan="6" align="center"><b>AUNCUNE INFORMATION TROUV&Eacute;E POUR CE D&Eacute;C&Egrave;S</b></td>
          </tr>`;
      } else {
        for (obj in param) {
          content += `
          <tr>
          <td>${i}</td>
          <td>${obj.nom_adherent} ${obj.prenom_adherent}</td>
          <td>${obj.contact_adherent}</td>
          <td>${obj.email_adherent}</td>
          <td>${obj.commune}</td>
          <td>${obj.MONTANT_COTISATION}</td>
          </tr>`;
          i++;
        }
      }

      while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
      }
      tbody.innerHTML = content
    }
  </script>
</body>
<!-- Mirrored from www.bootstrapdash.com/demo/skydash/template/demo/horizontal-default-light/pages/tables/data-table.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 31 Aug 2021 13:11:32 GMT -->

</html>