<?php
require_once 'config.php';

if(!isset($_SESSION['admin_id'])){
    header('location: index.php');
    exit();
}

?>

<!-- 7:05 cysec video zastani sme ! -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
</head>
<body>

<?php if(isset($_SESSION['success_message'])) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        echo $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

    </div>
<?php endif; ?>



<div class="container">

    <div class="row">
        <div class="col-md-12">
            <h2>Members List</h2>

            <a href="export.php?what=members" class="btn btn-success btn-sm">Export</a>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Trainer</th>
                        <th>Photo</th>
                        <th>Training PLan</th>
                        <th>Access Card</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $sql = "SELECT members.*,
                    training_plans.name AS training_plan_name,
                    trainers.first_name AS trainer_first_name,
                    trainers.last_name AS trainer_last_name
                    FROM `members`
                    LEFT JOIN `training_plans` ON members.training_plan_id = training_plans.plan_id
                    LEFT JOIN `trainers` ON members.trainer_id = trainers.trainer_id;";
                    $run = $conn->query($sql);
                    $results = $run->fetch_all(MYSQLI_ASSOC);

                    $select_member = $results;
                    
                    foreach($results as $result) : ?>

                        <tr>
                            <td><?php echo $result['first_name']; ?></td>
                            <td><?php echo $result['last_name']; ?></td>
                            <td><?php echo $result['email']; ?></td>
                            <td><?php echo $result['phone_number']; ?></td>
                            <td><?php 
                            if($result['trainer_first_name']){
                                echo $result['trainer_first_name'] . " " . $result['trainer_last_name']; 
                            }else{
                                echo "No trainer";
                            }
                            
                            ?></td>
                            <td><img style="width: 60px;" src="<?php echo $result['photo_path']; ?>"></td>
                            <td><?php 
                            if($result['training_plan_name']){
                                echo $result['training_plan_name'];
                            }else{
                                echo "No plan selected";
                            }
                            ?></td>

                            <td><a target="_blank" href="<?php echo $result['access_card_pdf_path']; ?>">Access Card</a></td>
                            
                            <td><?php 
                            
                            $created_at = strtotime($result['created_at']); 
                            $new_date = date("F, jS Y", $created_at);
                            echo $new_date;
                            
                            ?></td>
                            <td>
                                

                            <form action="delete_member.php" method="POST">
                                <input type="hidden" name="member_id" value="<?php echo $result['member_id']; ?>">
                                <button class="btn btn-danger" type="submit">DELETE</button>
                            </form>                        
                            </td>
                        
                        </tr>


                    <?php endforeach; ?>

                </tbody>
            </table>

            

        </div>
        <div class="col-md-12">
            <h2>Trainers List</h2>
            <a href="export.php?what=trainers" class="btn btn-success btn-sm">Export</a>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                    </tr>
                </thead>
                <tbody>
                
                <?php
                
                $sql= "SELECT * FROM trainers";
                $run = $conn->query($sql);
                $results = $run->fetch_all(MYSQLI_ASSOC);

                $select_trainers = $results;

                foreach($results as $result) : ?>
                    <tr>
                        <td><?php echo $result['first_name']; ?></td>
                        <td><?php echo $result['last_name']; ?></td>
                        <td><?php echo $result['email']; ?></td>
                        <td><?php echo $result['phone_number']; ?></td>
                        <td><?php echo date("F, jS Y", strtotime($result['created_at']));?></td>
                        <td>
                            <form action="delete_trainer.php" method="POST">
                                <input type="hidden" name="trainer_id" value="<?php echo $result['trainer_id']; ?>">
                                <button class="btn btn-danger" type="submit">DELETE</button>
                            </form>                        
                        </td>

                    </tr>

                <?php endforeach; ?>
                

                
                </tbody>
                </table>
        </div>                    

    </div>

    <div class="row mb-5">
        <div class ="col-md-6">
            <h2>Register Member</h2>
            <form action="register_member.php" method="POST" enctype="multipart/form-data">
                First Name: <input class="form-control" type="text" name="first_name"><br>
                Last Name: <input class="form-control" type="text" name="last_name"><br>
                Email: <input class="form-control" type="email" name="email"><br>
                Phone Number: <input class="form-control" type="text" name="phone_number"><br>
                Training Plan:
                <select class = "form-control" name="training_plan_id">
                    <option value="" disabled selected >Training Plan</option>
                    <?php
                        $sql = "SELECT * FROM training_plans";
                        $run = $conn->query($sql);
                        $results =  $run->fetch_all(MYSQLI_ASSOC);
                    
                        foreach ($results as $result){
                            echo "<option value='" .$result['plan_id'] ."'>" . $result['name'] . "</option>"; 
                        }
                    ?>

                    </select><br>
                    <input type="hidden" name="photo_path" id="photoPathInput">
                    <div id="dropzone-upload" class="dropzone"></div>

                    <input class="btn btn-primary mt-3" type="submit" value="Register Member">
                    </form>
        </div>
        <div class="col-md-6">
        <h2>Register Trainer</h2>
            <form action="register_trainer.php" method="POST">
                First Name: <input class="form-control" type="text" name="first_name"><br>
                Last Name: <input class="form-control" type="text" name="last_name"><br>
                Email: <input class="form-control" type="email" name="email"><br>
                Phone Number: <input class="form-control" type="text" name="phone_number"><br>
                <input class="btn btn-primary" type="submit" value="Register Trainer">
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h2>Assign Trainer to Member</h2>
            <form action="assign_trainer.php" method="POST">
                <label for="">Select Member</label>
                <select name="member" class="form-select">
                    <?php 
                    foreach($select_member as $member) :?>
                        <option value="<?php echo $member['member_id']?>">
                            <?php echo $member['first_name'] . " " . $member['last_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="">Select Trainer</label>
                <select name="trainer" class="form-select">
                    <?php 
                    foreach($select_trainers as $trainer) :?>
                        <option value="<?php echo $trainer['trainer_id']?>">
                            <?php echo $trainer['first_name'] . " " . $trainer['last_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary" type="submit">Assign Trainer</button>

            </form>
        </div>
    </div>

</div>

<?php $conn->close(); ?>

<!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
    Dropzone.options.dropzoneUpload = {
        url: "upload_photo.php",
        paramName: "photo",
        maxFilesize: 20,//MB
        acceptedFiles: "image/*",
        init: function(){
            this.on("success", function(file, response){
                const jsonResponse = JSON.parse(response);
                if(jsonResponse.success){
                    document.getElementById('photoPathInput').value = jsonResponse.photo_path;
                }
                else{
                    console.error(jsonResponse.error);
                }
            });
        }
    };
</script>
</body>
</html>