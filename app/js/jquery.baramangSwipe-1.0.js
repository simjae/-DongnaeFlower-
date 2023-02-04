/*!
 * BaramangSwipe jQuery Plugin v1.0
 * http://lab.zzune.com
 * https://github.com/rioald/BaramangSwipe
 *
 * Baramang(Banana, Lime, Mango) is originate by jjaom
 * special thanks to gramakson, hyo
 *
 * Copyright (c) 2011 zune-seok Moon (zune rioald).
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * 
 * Depends on TouchSwipe (jquery.touchSwipe v1.2.5)
 * Matt Bryson (www.skinkers.com)
 * http://plugins.jquery.com/project/touchSwipe
 * http://labs.skinkers.com/touchSwipe/
 * https://github.com/mattbryson/TouchSwipe-Jquery-Plugin
 *
 * Date: Wed Dec 28 11:38:36 2011 +0900
 */
var BaramangSwipe = {
        template: {},
        action: {}
};


(function($) {


BaramangSwipe.model = function(obj, elements, options) {
        var self = this;
        
        this.obj = obj;
        this.elementsSelector = elements;
        this.elements = this.obj.find(this.elementsSelector);
        this.lock = false;
        this.containerX = 0;
        
        // ������ �׷� �� element�� ��� �� ���� ����
        this.elementWidth = options.elementWidth || this.elements.eq(0).width();
        this.elementCount = this.elements.length;
        this.elementCountPerGroup = options.elementCountPerGroup || 999;
        this.maxElementGroup = options.elementCountPerGroup ? Math.ceil(this.elementCount / options.elementCountPerGroup) : this.elementCount;
        this.currentPositionGroup = options.currentPositionGroup || 0;
        this.currentPageNo = 0;
        
        // private var
        var isLoop = options.isLoop || false;
        var isAutoScroll = false;
        var autoScrollInterval = null;
        
        this.options = {
                elementWidth: options.elementWidth || this.elements.eq(0).width(),
                elementCountPerGroup: options.elementCountPerGroup || 999,
                speed: options.speed || 500,
                isLoop: options.isLoop || false,
                isAutoScroll: options.isAutoScroll || false,
                autoScrollDirection: options.autoScrollDirection == "left" ? "left" : "right",
                autoScrollTime: options.autoScrollTime || 9000
        };
        
        this.swipeOptions = $.extend({
                triggerOnTouchEnd : true,        
                swipeStatus : function(event, phase, direction, distance) { 
                                                self.swipeStatus(event, phase, direction, distance); 
                                          },
                allowPageScroll: "vertical",
                threshold: (navigator.userAgent.search("Android") > -1) ? 15 : 75,
                click: function(e, v) {
                        var a = $(v).parents("a");
                        
                        if(a.length > 0) {
                                var isDefaultAnchor = a.get(0).onclick ? a.get(0).onclick() : true;
                                
                                if(isDefaultAnchor) {
                                        window.location.href = a.eq(0).attr("href");
                                }
                        }
                }
        }, options.swipeOptions);
        
        this._initDimension = function() {
                // ���� �����̳��� width�� element�� �ִ�ũ��
                var maxElementWidth = self.obj.parent().width();
                
                // element�� �θ� (�þ �κ�)
                self.elementWidth = Math.min(self.options.elementWidth, maxElementWidth);
                self.obj.width(self.elementWidth * self.elementCount);
                
                // �����̳��� �ִ� ũ�⺸�� ������ element�� width�� �� Ŭ �� ����
                var possibleElementCountPerGroup = Math.floor(maxElementWidth / self.elementWidth);
                
                if(self.options.elementCountPerGroup > possibleElementCountPerGroup) {
                        self.elementCountPerGroup = possibleElementCountPerGroup;
                } else {
                        self.elementCountPerGroup = self.options.elementCountPerGroup;
                }
                
                // �ִ� element group������ ����
                self.maxElementGroup = Math.ceil(self.elementCount / self.elementCountPerGroup);
                
                // element�� ũ�⸦ �缳����
                // element group������ 1������ ��쿡�� ������ elementWidth�� �����ϰ� ũ�⿡ �°� ������
                self.elementWidth = Math.ceil(maxElementWidth / Math.min(self.elementCountPerGroup, self.elementCount));
                self.elements.width(self.elementWidth);
                
                // element group������ 1������ ��쿡�� loop�� �� ����
                if(self.maxElementGroup <= 1 && self.options.isLoop) {
                        isLoop = false;
                } else {
                        isLoop = self.options.isLoop;
                }
                
                $.each(self.elements, function(i, v) {
                        $(v)
                                .attr("elementindex", $(v).attr("elementindex") || i)
                                .attr("elementposition", self.containerX + self.elementWidth * i)
                                .css({
                                        "position": "absolute",
                                        "left": self.containerX + self.elementWidth * i,
                                        "-webkit-transform": "translateZ(0px)"                
                                })
                        .show();
                });
                
                // absolute position�� ��� box�� ����� ���ư��Ƿ� height�� �����Ͽ� ������Ŵ
                // img�� �ִ� ��� height����� ����� �̷������ ���� �� �����Ƿ� img�� onload event���
                self._initHeight && self._initHeight();
                self.obj.height(self.elements.eq(0).outerHeight());
        };
        
        this._initHeight = function() {
                var imgs = self.obj.parent().find("img");
                
                if(imgs.length > 0) {
                        imgs.bind("load", function() {
                                var outerHeight = self.elements.outerHeight();
                                
                                if(self.obj.height() <= outerHeight) {
                                        self.obj.height(outerHeight);
                                }
                        });
                }


                // �ѹ��� ����!
                self._initHeight = null;
        };
        
        this._initShadow = function() {
                var parent = self.elements.parent();
                
                // make shadow elements!!!
                // shadowBefore�� ��� ��쿡�� �����ؾ���
                var shadowBefore = $(self.elements.slice(-(self.elementCountPerGroup)).clone().get().reverse());
                $.each(shadowBefore, function(i, v) {
                        $(v)
                                .attr("elementposition", self.containerX + -self.elementWidth - self.elementWidth * i)
                                .css({
                                        left: self.containerX + -self.elementWidth - self.elementWidth * i
                                });
                });
                
                parent.prepend(shadowBefore.get().reverse());
                
                // shadowAfter�� �迭�� �ϼ����� �ƴ� ��쿡�� �����ؾ��Ѵ�
                if(self.elementCount % self.elementCountPerGroup != 0) {
                        
                        // ��ϼ��迭�� �ϼ������� �޲ٰ� �� �� �ϳ��� �ϼ��׷��� �� �߰����ش�
                        var shadowAfter = self.elements.slice(0, self.elementCountPerGroup + (self.elementCount % self.elementCountPerGroup)).clone();
                        $.each(shadowAfter, function(i, v) {
                                $(v)
                                        .attr("elementposition", self.containerX + self.elementWidth * self.elementCount + self.elementWidth * i)
                                        .css({
                                                left: self.containerX + self.elementWidth * self.elementCount + self.elementWidth * i 
                                        });
                        });
                        
                        parent.append(shadowAfter);
                }
                
                // re-select elements
                self.elements = self.obj.find(self.elementsSelector);                
        };
        
        this._init = function() {
                // dimension
                self._initDimension();
                
                // shadow
                // shadow�� �׷��� 1�̻��� ���� isLoop�� ��츸 �ʱ�����Ѵ�
                if(self.maxElementGroup > 1 && isLoop) {
                        self._initShadow();
                }
                
                // auto scroll
                if(self.options.isAutoScroll) {
                        self.activateAutoScroll();
                }
        };
        
        /**
         * swipe plugin�� ����� �ҷ��´�
         * load�� �ϱ��� swipeOptions�� �ɼ��� ��� ������ �ξ�� �Ѵ�
         */
        this.load = function(callback) {
                self._init();
                
                self.scrollElements = self.scrollElementsByTranslate;
                
                // load touchSwipe
                self.obj.swipe(self.swipeOptions);
                
                if(callback && callback instanceof Function) {
                        callback(self);
                }
                
                return self;
        };
        
        /**
         * element���� ��� ���ġ�Ѵ�
         */
        this.reload = function(callback) {
                if(isLoop) {
                        // ���� �������� �迭���� ���� �迭ũ����� ���ܵΰ� ��� ���� 
                        self.elements.not(self.elements.slice(self.elementCountPerGroup, self.elementCountPerGroup + self.elementCount)).remove();
                        
                        // element left�� ������
                        var firstElementX = parseInt(self.elements.eq(0).css("left"));                
                        $.each(self.elements, function(i, v) {
                                $(v)
                                        .attr("elementposition", firstElementX + self.elementWidth * i)
                                        .css({
                                                left: firstElementX + self.elementWidth * i
                                        });
                        });
                        
                        // element���� �ٽ� �����ϰ�
                        self.elements = self.obj.find(self.elementsSelector);                
                        
                        // setTimeout�� ���� multi-threadȿ���� �� (���� �ڿ������� ���̱� ����..)
                        setTimeout(function() {                        
                                // �ʱ�ȭ����
                                self._init();
                                
                                // left��ġ�� ������
                                // group�� �ϳ��� �ִ� ��쿡�� ���� �� element�� �̵���Ŵ
                                if(self.maxElementGroup <= 1) {
                                        self.scrollElements(self.containerX = parseInt(self.elements.eq(0).css("left")), 0);
                                        self.currentPageNo = 0;
                                } else {
                                        self.scrollElements(self.containerX = parseInt(self.elements.eq(self.elementCountPerGroup).css("left")), 0);
                                }
                                
                                if(callback && callback instanceof Function) {
                                        callback(self);
                                }                                
                        }, 0);
                }
                
                else {
                        // element left�� ������
                        var firstElementX = parseInt(self.elements.eq(0).css("left"));                
                        $.each(self.elements, function(i, v) {
                                $(v)
                                        .attr("elementposition", firstElementX + self.elementWidth * i)
                                        .css({
                                                left: firstElementX + self.elementWidth * i
                                        });
                        });
                        
                        // element���� �ٽ� �����ϰ�
                        self.elements = self.obj.find(self.elementsSelector);                
                        
                        // setTimeout�� ���� multi-threadȿ���� �� (���� �ڿ������� ���̱� ����..)
                        setTimeout(function() {                        
                                // �ʱ�ȭ����
                                self._init();
                                
                                // left��ġ�� ������
                                // group�� �ϳ��� �ִ� ��쿡�� ���� �� element�� �̵���Ŵ
                                if(self.maxElementGroup <= 1) {
                                        self.scrollElements(self.containerX = parseInt(self.elements.eq(0).css("left")), 0);
                                        self.currentPageNo = 0;
                                } else {
                                        self.scrollElements(self.containerX = parseInt(self.elements.eq(self.elementCountPerGroup).css("left")), 0);
                                }
                                
                                if(callback && callback instanceof Function) {
                                        callback(self);
                                }                                
                        }, 0);
                }
        };
        
        /**
         *         ���������� swipe�Ǿ� left, right�� �ߵ��Ǿ����� �ʿ��� ���� �����ۼ�
         */
        this.success = function() {};
        
        /**
         * mouse�� scrolling�� ��� contextmenu�� ��Ÿ���� �ʵ��� ��ġ�Ѵ�
         * swipe�� �Ǿ������� contextmenu�� ��Ÿ���� �ʵ��� �ϴ°��� ����
         */
        this.contextMenuEventHandler = function(event) { return false; };        
        
        /**
         * Catch each phase of the swipe.
         * move : we drag the div.
         * cancel : we animate back to where we were
         * end : we animate to the next element
         */
        this.swipeStatus = function(event, phase, direction, distance) {                
                self.swipeStatusByTranslate(event, phase, direction, distance);
                self.scrollElements = self.scrollElementsByTranslate;
                
                // swipe�� �Ǿ������� contextmenu�� ��Ÿ���� �ʵ��� �ϴ°��� ����
                if(phase == "end") {
                        self.obj.get(0).oncontextmenu = self.contextMenuEventHandler;
                } else {
                        self.obj.get(0).oncontextmenu = null;
                }
        };
        
        this.swipeStatusByTranslate = function(event, phase, direction, distance) {
                var currentX = self.containerX;
                
                // If we are moving before swipe, and we are going Lor R in X mode, or U or D in Y mode then drag.
                // ���Ʒ��� ��ũ�� �Ҷ��� �۵����� �ʵ��� ���´�
                if(phase == "move" && (direction == "up" || direction == "down")) {
                        self.lock = true;
                        
                        if(distance > 0) {
                                self.scrollElements(currentX, self.options.speed);
                        }
                        
                        return;
                } 
                
                else if(self.lock && (phase == "cancel" || phase == "end")) {
                        self.lock = false;
                        
                        return;
                }
                
                else if(self.lock) {
                        return;
                }
                
                else if(phase == "move" && (direction == "left" || direction=="right")) {
                        // auto scroll
                        if(self.options.isAutoScroll) {
                                self.deactivateAutoScroll();
                        }                        
                        
                        var duration = 0;
                        
                        if(direction == "left") {
                                self.scrollElements(currentX + distance, duration);
                        }
                        
                        else if(direction == "right") {
                                self.scrollElements(currentX - distance, duration);
                        }
                }
                
                else if(phase == "cancel") {
                        self.scrollElements(currentX, self.options.speed);
                        
                        // auto scroll
                        if(self.options.isAutoScroll) {
                                self.activateAutoScroll();
                        }                                
                }
                
                else if(phase == "end") {
                        if(direction == "right") {
                                self.previousElement();
                        } else if(direction == "left") {                        
                                self.nextElement();
                        }
                        
                        // auto scroll
                        if(self.options.isAutoScroll) {
                                self.activateAutoScroll();
                        }                                
                }
        };
        
        this.previousElement = function(times, isExecuteSuccess, speed) {
                times = times || 1;
                isExecuteSuccess = isExecuteSuccess || true;
                speed = speed || self.options.speed;
                
                if(isLoop) {
                        self.currentPositionGroup -= times;
                        self.currentPageNo = self.currentPageNo - times;
                        
                        if(self.currentPageNo <= -1) {
                                self.currentPageNo = self.maxElementGroup + self.currentPageNo;
                        } else if(self.currentPageNo >= self.maxElementGroup) {
                                self.currentPageNo = self.currentPageNo - self.maxElementGroup;
                        }        
                        
                        var currentX = self.containerX = self.containerX - self.elementWidth * self.elementCountPerGroup * times;
                        self.scrollElements(currentX, speed);
                        
                        // make shadow elements!!!
                        // ���� �� element�� X��ǥ
                        var firstElementX = parseInt(self.elements.eq(0).css("left"));
                        
                        // ���� �� element�� index��ȣ
                        var firstElementIndex = parseInt(self.elements.eq(0).attr("elementindex")) - self.elementCountPerGroup;
                        if(firstElementIndex < 0) {
                                firstElementIndex = (self.elementCount) + firstElementIndex;
                        }
                        
                        // �տ��� ���� shadow element�� ���� �� ���� element�� ����ŭ�� ��Ƶд� 
                        var shadowBeforeTemp = self.elements.slice(-(self.elementCount));
                        
                        // ������ �Ǵ� index�� ã�´�
                        var shadowBeforeBaseIndex = 0;
                        shadowBeforeTemp.each(function(i, v) {
                                if($(v).attr("elementindex") == firstElementIndex) {
                                        shadowBeforeBaseIndex = i;
                                        return;
                                }
                        });


                        // ����index�� �������� slice�Ѵ�
                        var shadowBefore = shadowBeforeTemp.slice(shadowBeforeBaseIndex, shadowBeforeBaseIndex + self.elementCountPerGroup).clone();
                        
                        // slice�� array�� �׷� element���� ���� ��� �� �տ��� ������ ����ŭ ä���ִ´�
                        if(shadowBefore.length < self.elementCountPerGroup) {
                                shadowBefore = shadowBefore.add(shadowBeforeTemp.slice(0, self.elementCountPerGroup - shadowBefore.length).clone());
                        }
                        
                        // swipe�� ���ÿ� element��ġ�� handling�ϹǷ� �Ҷ� ����µ��� ������ �־�
                        // setTimeout�� ���� multi-threadȿ���� ��
                        setTimeout(function() {
                                $.each(shadowBefore, function(i, v) {
                                        $(v)
                                                .attr("elementposition", firstElementX + -self.elementWidth - self.elementWidth * (shadowBefore.length - 1 - i))
                                                .css({
                                                        "left": firstElementX + -self.elementWidth - self.elementWidth * (shadowBefore.length - 1 - i)
                                                });
                                });
                                
                                self.elements.parent().prepend(shadowBefore);
                                
                                // ���� �� �׷��� �����Ѵ�
                                self.elements.slice(-(self.elementCountPerGroup)).remove();
                                self.elements = self.obj.find(self.elementsSelector);
                        }, 0);                                
                } 
                
                else {
                        if(self.currentPageNo <= 0) {
                                self.scrollElements(self.containerX, speed);                        
                        }
                        
                        else {
                                self.currentPositionGroup = Math.max(self.currentPositionGroup - times, 0);
                                self.currentPageNo = Math.max(self.currentPageNo - times, 0);
                                
                                var currentX = self.containerX = self.containerX - self.elementWidth * self.elementCountPerGroup * times;
                                self.scrollElements(currentX, speed);                        
                        }
                }
                
                if(isExecuteSuccess) {
                        setTimeout(self.success, 0);
                }

				var chiceIndex = self.currentPageNo+1;
				var lastIndex = self.elementCount-1;
				if(self.currentPageNo == lastIndex ){
					$(".naviImg0").attr("src","/m/images/poff.png");
				}else{
				
				$(".naviImg"+chiceIndex).attr("src","/m/images/poff.png");
				}
        };
        
        this.nextElement = function(times, isExecuteSuccess, speed) {
                times = times || 1;
                isExecuteSuccess = isExecuteSuccess || true;
                speed = speed || self.options.speed;                
                
                if(isLoop) {
                        self.currentPositionGroup += times;
                        self.currentPageNo = self.currentPageNo + times;
                        
                        if(self.currentPageNo <= -1) {
                                self.currentPageNo = self.maxElementGroup + self.currentPageNo;
                        } else if(self.currentPageNo >= self.maxElementGroup) {
                                self.currentPageNo = self.currentPageNo - self.maxElementGroup;
                        }
                        
                        var currentX = self.containerX = self.containerX + self.elementWidth * self.elementCountPerGroup * times;                
                        self.scrollElements(currentX, speed);
                        
                        // make shadow elements!!!
                        // ���� �� element�� X��ǥ
                        var lastElementX = parseInt(self.elements.eq(-1).css("left"));
                        
                        // ���� �� element�� index��ȣ
                        var lastElementIndex = parseInt(self.elements.eq(-1).attr("elementindex")) + 1;
                        if(lastElementIndex >= self.elementCount) {
                                lastElementIndex = lastElementIndex - self.elementCount;
                        }
                        
                        // �տ��� ���� shadow element�� ���� �� ���� element�� ����ŭ�� ��Ƶд� 
                        var shadowAfterTemp = self.elements.slice(0, self.elementCount);
                        
                        // ������ �Ǵ� index�� ã�´�
                        var shadowAfterBaseIndex = 0;
                        shadowAfterTemp.each(function(i, v) {
                                if($(v).attr("elementindex") == lastElementIndex) {
                                        shadowAfterBaseIndex = i;
                                        return;
                                }
                        });


                        // ����index�� �������� slice�Ѵ�
                        var shadowAfter = shadowAfterTemp.slice(shadowAfterBaseIndex, shadowAfterBaseIndex + self.elementCountPerGroup).clone();
                        
                        // slice�� array�� �׷� element���� ���� ��� �� �տ��� ������ ����ŭ ä���ִ´�
                        if(shadowAfter.length < self.elementCountPerGroup) {
                                shadowAfter = shadowAfter.add(shadowAfterTemp.slice(0, self.elementCountPerGroup - shadowAfter.length).clone());
                        }
                        
                        // swipe�� ���ÿ� element��ġ�� handling�ϹǷ� �Ҷ� ����µ��� ������ �־�
                        // setTimeout�� ���� multi-threadȿ���� ��
                        setTimeout(function() {
                                $.each(shadowAfter, function(i, v) {
                                        $(v)
                                                .attr("elementposition", lastElementX + self.elementWidth * (i + 1))
                                                .css({
                                                        "left": lastElementX + self.elementWidth * (i + 1) 
                                                });
                                });
                                
                                self.elements.parent().append(shadowAfter);
                                
                                // ���� �� �׷��� �����Ѵ�
                                self.elements.slice(0, self.elementCountPerGroup).remove();
                                self.elements = self.obj.find(self.elementsSelector);
                        }, 0);        
                        
                } 
                
                else {
                        if(self.currentPageNo >= self.maxElementGroup - 1) {
                                self.scrollElements(self.containerX, speed);                        
                        }
                        
                        else {
                                self.currentPositionGroup = Math.min(self.currentPageNo + 1, self.maxElementGroup - 1);
                                self.currentPageNo = Math.min(self.currentPageNo + 1, self.maxElementGroup - 1);
                                
                                var currentX = self.containerX = self.containerX + self.elementWidth * self.elementCountPerGroup * times;                
                                self.scrollElements(currentX, speed);
                        }                        
                }
                
                if(isExecuteSuccess) {
                        setTimeout(self.success, 0);
                }
				var choisIndex = self.currentPageNo-1;
				var lastIndex = self.elementCount-1;
				if(self.currentPageNo == 0 ){
					$(".naviImg"+lastIndex).attr("src","/m/images/poff.png");
				}else{
					$(".naviImg"+choisIndex).attr("src","/m/images/poff.png");
				}
        };
        
        /**
         * �ش� index��ȣ�� �̵��Ѵ�
         * @param index
         */
        this.moveTo = function(index, isExecuteSuccess, speed) {
                var times = 0;
                
                if(index > self.currentPageNo) {
                        times = Math.min(index - self.currentPageNo, (self.maxElementGroup - 1) - self.currentPageNo);
                        
                        if(times > 0) {
                                var currentIndex = self.elements.slice(self.elementCountPerGroup).filter("[elementindex=" + self.currentPageNo * self.elementCountPerGroup + "]").index();        
                                var beforeGroupCount = Math.ceil(self.elements.slice(0, currentIndex).length / self.elementCountPerGroup) - 1;
                                var afterGroupCount = Math.ceil(self.elements.slice(currentIndex).length / self.elementCountPerGroup) - 1;
                                
                                if(times > afterGroupCount) {
                                        self.previousElement(self.maxElementGroup - times, isExecuteSuccess, speed);
                                } else {
                                        self.nextElement(times, isExecuteSuccess, speed);
                                }
                        }
                } 
                
                else if(index < self.currentPageNo) {
                        times = Math.min(self.currentPageNo - index, self.currentPageNo);
                        
                        if(times > 0) {
                                var currentIndex = self.elements.slice(self.elementCountPerGroup).filter("[elementindex=" + self.currentPageNo * self.elementCountPerGroup + "]").index();        
                                var beforeGroupCount = Math.ceil(self.elements.slice(0, currentIndex).length / self.elementCountPerGroup) - 1;
                                var afterGroupCount = Math.ceil(self.elements.slice(currentIndex).length / self.elementCountPerGroup) - 1;
                                
                                if(times > beforeGroupCount) {
                                        self.nextElement(self.maxElementGroup - times, isExecuteSuccess, speed);
                                } else {
                                        self.previousElement(times, isExecuteSuccess, speed);
                                }                                
                        }
                }
        };
        
        this.getCurrentElements = function() {
                var index = self.elements.filter("[elementposition='" + self.getCurrentElementPosition() + "']").index();
                var currentElementGroup = Math.floor(index / self.elementCountPerGroup);
                
                return self.elements.slice(currentElementGroup * self.elementCountPerGroup, (currentElementGroup + 1) * self.elementCountPerGroup);                
        };
        
        this.getCurrentElementPosition = function() {
                return self.obj.attr("elementposition") || 0;
        };
        
        this.activateAutoScroll = function() {
                // group�� �ϳ��ΰ��� autoScroll�Ұ�
                if(self.maxElementGroup == 1) {
                        if(autoScrollInterval) {
                                clearInterval(autoScrollInterval);
                        }                        
                        
                        return;
                }
                
                if(autoScrollInterval) {
                        clearInterval(autoScrollInterval);
                }
                
                if(self.options.autoScrollDirection == "left") {
                        if(!isLoop && self.currentPageNo == 0) {
                                self.options.autoScrollDirection = "right";
                                self.activateAutoScroll();
                                
                                return;
                        }
                        
                        autoScrollInterval = setInterval(function() { 
                                self.previousElement(); self.activateAutoScroll(); 
                        }, self.options.autoScrollTime);
                } 
                
                else {
                        if(!isLoop && self.currentPageNo == (self.maxElementGroup - 1)) {
                                self.options.autoScrollDirection = "left";
                                self.activateAutoScroll();
                                
                                return;
                        }                        
                        
                        autoScrollInterval = setInterval(function() { 
                                self.nextElement(); self.activateAutoScroll(); 
                        }, self.options.autoScrollTime);
                }
        };
        
        this.deactivateAutoScroll = function() {
                if(autoScrollInterval) {
                        clearInterval(autoScrollInterval);
                }
        };
        
        /**
         * window�� ����� �ٲ𶧸��� �̹����� wrapper�� width�� ���������� �ٲ��� �ϹǷ�
         * ����� ������ �ۼ��Ͽ� �Լ��� �д� 
         */
        this.onresize = function() {
                if(self.currentWindowSize != $(window).width()) {
                        self.currentWindowSize = $(window).width();
                        self.reload();
                }
        };
        
        // bind window.onresize
        if(navigator.userAgent.search("iPhone|iPod|iPad") > -1) {
                $(window).bind("orientationchange", function() { self.onresize(); });        
        } else {
                $(window).bind("resize", function() { self.onresize(); });        
        }
        
        /**
         * element�� scroll�ϱ� ���� function, ��忡 ���� ������
         */
        this.scrollElements = function() {};
        
        /**
         * translate3d������� element�� scrolling�Ѵ�
         */
        this.scrollElementsByTranslate = function(distance, duration) {
                self.obj.css("-webkit-transition-duration", (duration / 1000).toFixed(1) + "s");
                
                distance = distance || 0;
                
                //inverse the number we set in the css
                var value = (distance < 0 ? "" : "-") + Math.abs(distance).toString();
                
                self.obj.attr("elementposition", -value);
                self.obj.css("-webkit-transform", "translate3d(" + value + "px, 0px, 0px)");
        };
        
        // �� ��ü�� action�� ������ش�
        BaramangSwipe.action[this.obj.attr("id") || new Date().getMilliseconds()] = this;
};


$.fn.baramangSwipe = function(elements, options) {
        this.baramangSwipe = new BaramangSwipe.model($(this), elements, options);
        
        return this.baramangSwipe;
};


})(jQuery);

