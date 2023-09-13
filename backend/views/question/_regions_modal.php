<?php
use backend\assets\SvgAsset;
use backend\assets\TestQuestionAsset;
/** @var $model backend\models\question\UpdateRegionQuestion */
/** @var $this yii\web\View */
$css = <<< CSS
#regions-modal .modal-dialog {
    max-width: 100%;
    width: auto !important;
    display: inline-block;
}
#regions-modal {
  z-index: -1;
  display: flex !important;
  justify-content: center;
  margin: 0 auto;
}
.modal-open .in#regions-modal {
   z-index: 1050;
}
.image-container-wrapper {
    min-height: 500px;
    min-width: 800px;
}
#regions-modal .alert {
    padding: 6px;
}
.modal-lg {
    max-width: 80% !important;
}
CSS;
$this->registerCss($css);
SvgAsset::register($this);
TestQuestionAsset::register($this);
?>
<div class="modal fade" id="regions-modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Regions</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
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
                    <div class="col-md-7">
                        <div class="alert alert-info" role="alert" style="font-size:1.5rem;margin-bottom:2px">Для удаления области, выделите ее и нажмите DEL</div>
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
    /*selectShapes.on('change', function() {
        shapeType.setType($(this).find("input[name='shape']:checked").val());
    });*/

    var element = $('#updateregionquestion-regions');
    var regionSVG;

    modal.on('show.bs.modal', function() {

        selectShapes.button('reset');

        let data = element.val() || [];
        if (typeof data === 'string') {
            data = JSON.parse(data);
        }

        if (regionSVG === undefined) {
            regionSVG = new RegionsSVG('image-container');
        }

        regionSVG.loadImage('$imagePath', $imageWidth, $imageHeight, data, (args) => {

            regionSVG.drawRect();

          selectShapes.on('change', function() {
            const val = $(this).find("input[name='shape']:checked").val();
            switch (val) {
              case 'polyline':
                regionSVG.drawPolyline();
                break;
              case 'circle':
                regionSVG.drawCircle();
                break;
              case 'rect':
                regionSVG.drawRect();
                break;
            }
            });
        });
    });

    $('#save-regions', modal).on('click', function() {
        modal.modal('hide');
        element.val(JSON.stringify(regionSVG.getRegions()));
        $('#update-region-question-form').submit();
    });
JS;
$this->registerJs($js);
