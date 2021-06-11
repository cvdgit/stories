<?php
use backend\assets\SvgAsset;
use backend\assets\TestQuestionAsset;
/** @var $model backend\models\question\UpdateRegionQuestion */
/** @var $this yii\web\View */
$css = <<< CSS
body .modal-dialog {
    max-width: 100%;
    width: auto !important;
    display: inline-block;
}
.modal {
  z-index: -1;
  display: flex !important;
  justify-content: center;
}
.modal-open .modal {
   z-index: 1050;
}
.image-container-wrapper {
    min-height: 500px;
}
CSS;
$this->registerCss($css);
SvgAsset::register($this);
TestQuestionAsset::register($this);
?>
<div class="modal fade" id="regions-modal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Regions</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-group" id="select-shapes" data-toggle="buttons" style="margin-bottom: 20px">
                            <label class="btn btn-default active">
                                <input type="radio" name="shape" value="rect" autocomplete="off" checked> Прямоугольник
                            </label>
                            <label class="btn btn-default">
                                <input type="radio" name="shape" value="circle" autocomplete="off"> Круг
                            </label>
                            <label class="btn btn-default">
                                <input type="radio" name="shape" value="polyline" autocomplete="off"> Линия
                            </label>
                        </div>
                    </div>
                </div>
                <div class="image-container-wrapper">
                    <div id="image-container"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save-regions">Сохранить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<?php
$imagePath = $model->getImageUrl();
$imageWidth = $model->getImageWidth();
$imageHeight = $model->getImageHeight();
$js = <<< JS
    
    var modal = $('#regions-modal');
    var selectShapes = $('#select-shapes');
    
    function ShapeType(type) {
        this.type = type;
    }
    
    ShapeType.prototype = {
        'getType': function() {
            return this.type;
        },
        'setType': function(type) {
            this.type = type;
        },
        'isPolyline': function() {
            return this.type === 'polyline';
        }
    }

    var shapeType = new ShapeType('rect');
    selectShapes.on('change', function() {
        shapeType.setType($(this).find("input[name='shape']:checked").val());
    });
    
    var element = $('#updateregionquestion-regions');
    var regionSVG;    

    modal.on('show.bs.modal', function() {
        
        selectShapes.button('reset');
        
        if (regionSVG === undefined) {
            
            var data = element.val() || [];
            if (typeof data === 'string') {
                data = JSON.parse(data);
            }
            
            regionSVG = new RegionsSVG(
                'image-container', 
                {'path': '$imagePath', 'width': $imageWidth, 'height': $imageHeight}, 
                shapeType,
                data);
        }
    });
    
    $('#save-regions', modal).on('click', function() {
        modal.modal('hide');
        element.val(JSON.stringify(regionSVG.getRegions()));
    });
JS;
$this->registerJs($js);