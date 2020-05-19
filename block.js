//引入对应方法, 需要注意的是这里引用了4个方法, 那么在底部也需要window.wp.回调这4个方法
//这4个方法的来源是functions.php里的wp_register_script时array()里传入, 需要注意一一对应
(function (blocks, element, editor, i18n) {
	var el = element.createElement; //用于输出HTML
	var RichText = editor.RichText; //用于获取文本输入块

	var TextareaControl = wp.components.TextareaControl;

	blocks.registerBlockType('wp-plot/chart', {
		title: 'Text2Chart', //标题
		icon: 'chart-area', //图标
		category: 'layout', //对应栏目
		attributes: { //模块的属性
			chl: {
				default: ''
			},
			cht: {
				default: 'gv:dot'
			},
			caption: {
				default: 'WordPress Text2Chart'
			},
			chof: {
				default: 'png'
			},
			width: {
				default: ''
			},
			height: {
				default: ''
			},
			align: {
				default: 'center'
			}
		},
		//编辑时
		edit: function (props) {
			const attributes =  props.attributes;
			const setAttributes =  props.setAttributes;

			function onChangeCht(cht) {
				setAttributes({cht});
			}

			function onChangeChl(chl) {
				setAttributes({chl});
			}

			function onChangeChof(chof) {
				setAttributes({chof});
			}

			function onChangeCaption(caption) {
				setAttributes({caption});
			}

			function onChangeAlign(align) {
				setAttributes({align});
			}

			function onChangeWidth(width) {
				setAttributes({width});
			}

			function onChangeHeight(height) {
				setAttributes({height});
			}
			//返回HTML
			//el的方法格式为: el( 对象, 属性, 值 ); 可以相互嵌套
			//例如:
			// el(
			//	 'div',
			//	 {
			//		 className: 'demo-class',
			//	 },
			//	 'DEMO数据'
			// );
			// 输出为: <div class="demo-class">DEMO数据</div>
			return el('div',{},[
					el('div',{},[
						el(RichText.Content,{
							value: attributes.chl,
							tagName: 'pre'
						}),
					]),
					el(InspectorControls,{},[
						el(TextControl, {
							value: attributes.cht,
							label: __( 'Chart Engine' ),
							onChange: onChangeCht,
						}),
						el(TextareaControl, {
							value: attributes.chl,
							label: __( 'Code' ),
							onChange: onChangeChl
						}),
						el(TextControl, {
							value: attributes.caption,
							label: __( 'Caption' ),
							onChange: onChangeCaption,
						}),
						el(SelectControl, {
							value: attributes.chof,
							label: __( 'Output format' ),
							onChange: onChangeChof,
							options: [{value:'png',label:'png'},{value:'jpg',label:'jpg'},{value:'svg',label:'svg'},{value:'gif',label:'gif'}]
						}),
						el(TextControl, {
							value: attributes.align,
							label: __( 'Align' ),
							onChange: onChangeAlign,
						}),
						el(TextControl, {
							value: attributes.width,
							label: __( 'Width' ),
							onChange: onChangeWidth,
						}),
						el(TextControl, {
							value: attributes.height,
							label: __( 'Height' ),
							onChange: onChangeHeight,
						}),
					])
				]
			);
		},
		//保存时
		save: function (props) {
			var o = props.attributes.chl;
			o = o.replace(/</g, '&lt;');
			o = o.replace(/>/g, '&gt;');
			var plot = '[plot cht="' + props.attributes.cht + '"';
			plot +=	' chof="' + props.attributes.chof + '"';
			plot += ' caption="' + props.attributes.caption + '"';
			plot += ' align="' + props.attributes.align + '"';
			if ( props.attributes.width != '') plot += ' width=' + props.attributes.width;
			if ( props.attributes.height != '') plot += ' height=' + props.attributes.height;
			plot += '"]';
			plot += o;
			plot += '[/plot]';

			return el(RichText.Content,{tagName: 'div',value: plot});
		},
	});
}(
	window.wp.blocks,
	window.wp.element,
	window.wp.editor,
	window.wp.i18n
));
