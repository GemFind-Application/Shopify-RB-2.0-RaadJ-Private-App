<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Diamond Details</title>
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
    />
  </head>
  <body>
    <div class="personal-training-form">
      <div class="personal-training-heading" style="background-color:#1979c3; color: #fff;" >
        <h2>Diamond Detail &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?php echo date("d/m/Y"); ?></h2>
        <!-- <p>
          Lorem Ipsum is simply dummy text of the printing and typesetting
          industry. Lorem Ipsum has been the industry's standard dummy text ever
          since the 1500s, when an unknown printer took a galley of type and
          scrambled it to make a type specimen book.
        </p> -->
      </div>

      <!-- <div
      class="print-header"
      style="background-color: #1979c3 !important; color: #fff !important"
    >
      <div class="header-container">
        <div class="header" style="ali">
          <div class="header-title" style="text-align: left;">
            <h2 style="color: #fff !important">Diamond Detail</h2>
          </div>
          <div class="header-date" style="text-align: right;">
            <h4 style="color: #fff !important">16-04-2022</h4>
          </div>
        </div>
        
      </div>
    </div> -->

    <section class="diamonds-search with-specification diamond-page">
      <div class="d-container">
        <div class="d-row">
          <div
            class="diamonds-print-preview no-padding"style=" width: 100%; background-color: #f7f7f7 !important; border: 1px solid #e8e8e8 !important ; display: block; vertical-align: middle;">

              <div  style="width: 50%; float: left; padding-left: 30px; padding-top: 15px;">  
              <?php if(isset($diamond['diamondData']['image2'])) { ?>
                <img
                  src="<?php echo $diamond['diamondData']['image2'] ?>"
                  style="height: 100px; width: 100px"/>
               <?php } ?>      
               </div>
               <div style="width: 50%; float: left; padding-left: 80px; padding-top: 15px;">
                 <?php if(isset($diamond['diamondData']['image1'])) { ?>
                    <img
                    src="<?php echo $diamond['diamondData']['image1'] ?>"
                    style="height: 100px; width: 165px"/>
                 <?php } ?> 
               </div>

               

                 
               <div class="skunumber" style="width: 100%; text-align: center;"> 
                  <p>
                    SKU#
                    <span style="color: #1979c3;"><?php echo $diamond['diamondData']['diamondId'] ?></span>
                  </p>
               </div>
               
               
              </div>

              <!-- <div class="diamond-info-two">
                <img
                  src="https://apps.gemfind.net/dealerid_display/drawshape.aspx?shape=emerald&measurements=9.42X7.75X5.14&tablemeasure=66&depth=66.3&girdle=Medium&culet=Very Small"
                  style="height: 100px; width: 165px"
                />
              </div> -->
               </div>
               
              <!-- <div class="print-info" style="text-align: center;">
                  <p>
                    SKU#
                    <span>203379340</span>
                  </p>
              </div> -->
           </div>
        </div>
     </div>
  </section>

         &nbsp;&nbsp;&nbsp;
        <div class="personal-training-heading" style="background-color:#e8e8e8; color: #000; width: 100%; display: block; height: 100px; vertical-align: middle;"  >
         <div style="width: 10%; float: left; padding: 5px; ">
            <img
                src="<?php echo $diamond['diamondData']['certificateIconUrl'] ?>"
                style="height: 75px; width: 75px;">
         </div>
          <div style="width: 80%; float: left;">
             <p style=" text-align: left; padding-top: 18px; padding-left: 15px;"><?php echo $diamond['diamondData']['subHeader']; ?>
          </div>
                


        <!-- <p style="vertical-align: middle; width: 200px;">This G color, VS1 clarity diamond comes accompanied by a diamond grading report from GIA</p> -->
      
      </div>


      
      <div class="personal-training-heading" style="background-color:#1979c3; color: #fff; width: 100%; display: block;  height: 100px; vertical-align: middle;">
         <div style="width: 80%; float: left;">
        <h2 style="text-align: left;"><?php echo $diamond['diamondData']['mainHeader']; ?></h2>
        <p> <?php echo $diamond['diamondData']['subHeader']; ?> </p>
      </div>
      <div style="width: 10%; float: left; ">
         <p style="text-align: left; padding-top: 40px; padding-left: 50px;">
            <span><?php
               if($diamond['diamondData']['currencyFrom'] == 'USD'){
                  echo "$".number_format($diamond['diamondData']['fltPrice']);
               }else{
                  echo $diamond['diamondData']['currencyFrom'].$diamond['diamondData']['currencySymbol'].number_format($diamond['diamondData']['fltPrice']);
               }
            ?></span>
          </p>
         
      </div>
   </div>
   


      <div class="personal-training">
        <table class="table">
          <tbody>
            <?php if(isset($diamond['diamondData']['diamondId'])) { ?>
              <tr>
                <td><b><?php echo 'Stock Number'; ?></b></td>
                <td><?php echo $diamond['diamondData']['diamondId'] ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['fltPrice'])) { ?>
              <tr>
                <td><b><?php echo 'Price Per Carat'; ?></b></td>
                <td><?php 
                     // if($diamond['diamondData']['currencyFrom'] == 'USD'){
                     //    echo "$".number_format(str_replace(',', '', $diamond['diamondData']['fltPrice'])/$diamond['diamondData']['caratWeight']);    
                     // }else{
                     //    echo $diamond['diamondData']['currencyFrom'].$diamond['diamondData']['currencySymbol'].number_format(str_replace(',', '', $diamond['diamondData']['fltPrice'])/$diamond['diamondData']['caratWeight']); 
                     // }

                    if($diamond['diamondData']['currencyFrom'] == 'USD'){
                        echo "$".number_format(str_replace(',', '', $diamond['diamondData']['costPerCarat']));    
                     }else{
                        echo $diamond['diamondData']['currencyFrom'].$diamond['diamondData']['currencySymbol'].number_format(str_replace(',', '', $diamond['diamondData']['costPerCarat'])); 
                     }
                     ?>
                </td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['caratWeight'])) { ?>
              <tr>
                <td><b><?php echo 'Carat Weight'; ?></b></td>
                <td><?php echo $diamond['diamondData']['caratWeight'] ? $diamond['diamondData']['caratWeight'] : 'NA' ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['cut'])) { ?>
              <tr>
                <td><b><?php echo 'Cut'; ?></b></td>
                <td><?php echo $diamond['diamondData']['cut'] ? $diamond['diamondData']['cut'] : 'NA' ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['color'])) { ?>
              <tr>
                <td><b><?php echo 'Color'; ?></b></td>
                <td><?php echo $diamond['diamondData']['color'] ? $diamond['diamondData']['color'] : 'NA' ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['clarity'])) { ?>
              <tr>
                <td><b><?php echo 'Clarity'; ?></b></td>
                <td><?php echo $diamond['diamondData']['clarity'] ? $diamond['diamondData']['clarity'] : 'NA' ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['certificate'])) { ?>
              <tr>
                <td><b><?php echo 'Report'; ?></b></td>
                <td><?php echo $diamond['diamondData']['certificate'] ? $diamond['diamondData']['certificate'] : 'NA' ; ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['depth'])) { ?>
              <tr>
                <td><b><?php echo 'Depth %'; ?></b></td>
                <td><?php echo $diamond['diamondData']['depth'] ? $diamond['diamondData']['depth'] : 'NA' ; ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['table'])) { ?>
              <tr>
                <td><b><?php echo 'Table %'; ?></b></td>
                <td><?php echo $diamond['diamondData']['table'] ? $diamond['diamondData']['table'] : 'NA' ; ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['polish'])) { ?>
              <tr>
                <td><b><?php echo 'Polish'; ?></b></td>
                <td><?php echo $diamond['diamondData']['polish'] ? $diamond['diamondData']['polish'] : 'NA' ; ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['symmetry'])) { ?>
              <tr>
                <td><b><?php echo 'Symmetry'; ?></b></td>
                <td><?php echo $diamond['diamondData']['symmetry'] ? $diamond['diamondData']['symmetry'] : 'NA' ; ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['gridle'])) { ?>
              <tr>
                <td><b><?php echo 'Girdle'; ?></b></td>
                <td><?php echo $diamond['diamondData']['gridle'] ? $diamond['diamondData']['gridle'] : 'NA' ; ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['culet'])) { ?>
              <tr>
                <td><b><?php echo 'Culet'; ?></b></td>
                <td><?php echo $diamond['diamondData']['culet'] ? $diamond['diamondData']['culet'] : 'NA' ; ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['fluorescence'])) { ?>
              <tr>
                <td><b><?php echo 'Fluorescence'; ?></b></td>
                <td><?php echo $diamond['diamondData']['fluorescence'] ? $diamond['diamondData']['fluorescence'] : 'NA' ; ?></td>
              </tr>
            <?php } ?>
            <?php if(isset($diamond['diamondData']['measurement'])) { ?>
              <tr>
                <td><b><?php echo 'Measurement'; ?></b></td>
                <td><?php echo $diamond['diamondData']['measurement'] ? $diamond['diamondData']['measurement'] : 'NA' ; ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </body>
</html>
