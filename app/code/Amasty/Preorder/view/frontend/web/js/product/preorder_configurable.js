define([
    "jquery",
    "jquery/ui",
    'Amasty_Preorder/js/product/preorder'

], function($) {
    $.widget('mage.amastyPreorderConfigurable', $.mage.amastyPreorder, {
        options: {
            map: [],
            currentAttributes: {},
            isAllProductsPreorder: 0
        },
        _create: function(){
            this._saveOriginal();
            if(this.options.isAllProductsPreorder == 1) {
                this.enable();
            }
          //super._create();
            var self = this;
            //jQuery("#product_addtocart_form [name=product]").on('change', function(){console.log(this.value)});
            $('.swatch-opt').change(function(){
               self.update();
            });
            $('.swatch-opt').click(function(){
                self.update();
            });
        },
        update: function () {
            var attributeValue;
            var isChanged = false;
            for(var attributeId in this.options.currentAttributes) {
                attributeValue = this.options.currentAttributes[attributeId];
                var $element = $('[attribute-id='+attributeId+']');
                if(!$element.length) {
                    console.log('error');
                    return;
                }
                if($element.attr('option-selected') != attributeValue) {
                    isChanged = true;
                    this.options.currentAttributes[attributeId] = $element.attr('option-selected');
                }
            }
            if(isChanged) {
                this.onChangeProductAttributes();
            }
        },
        onChangeProductAttributes: function(){
            var currentProductId = false;
            for(var productId in this.options.map) {
                var productInfo = this.options.map[productId];
                currentProductId = productId;
                for(var attributeId in this.options.currentAttributes) {
                    attributeValue = this.options.currentAttributes[attributeId];
                    if(productInfo.attributes[attributeId] != attributeValue) {
                        currentProductId = false;
                        break;
                    }
                }
                if(currentProductId) {
                    break;
                }
            }

            if(this.options.map[currentProductId]) {
                this.options.addToCartLabel = this.options.map[currentProductId]['cartLabel'];
                this.options.preOrderNote = this.options.map[currentProductId]['note'];
                this.enable();
            } else {
                this.disable();
            }
        }
    }

    );
});
