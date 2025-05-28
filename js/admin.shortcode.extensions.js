// // let blocks = wp.blocks;
// // let element = wp.element;
// // let blockEditor = wp.blockEditor;
// // let el = element.createElement;

// let InspectorControls = wp.blockEditor.InspectorControls;
// let PanelBody = wp.components.PanelBody;
// // add a hook for blockedit
// // function addMedialibraryControls(blockEdit) {
// //     wp.compose.createHigherOrderComponent(function (props) {
// //         console.log(props);
// //         // return blockEdit(props);

// //         // return function (props) {

// //         //     // If this block supports scheduling and is currently selected, add our UI
// //         //     if (props.name != 'core/shortcode' || !props.isSelected) {
// //         //         return el('section');
// //         //     }

// //         //     console.log(props);

// //         //     return el('section');

// //         // }
// //         return el('section')
// //     });

// //     return blockEdit;
// // }
// let addMedialibraryControls = wp.compose.createHigherOrderComponent((BlockEdit) => {
//     return (blockProperties) => {
//         // we only want this control at the shortcodes.
//         if (blockProperties.name !== 'core/shortcode') { return BlockEdit(blockProperties); }
//         // only add it if the control is selected
//         if (!blockProperties.isSelected) return BlockEdit(blockProperties);

//         return wp.element.createElement('section', {}, wp.element.createElement(
//             InspectorControls,
//             {

//             },
//             wp.element.createElement(PanelBody, {
//                 title: 'Content',
//                 initialOpen: true,
//                 children: (props) => {
//                     return wp.element.createElement(wp.components.PanelRow, {},
//                         wp.element.createElement(wp.components.Button, {
//                             className: "components-button editor-post-featured-image__toggle",
//                             text: "Selecteer Afbeelding",
//                             onClick: (element) => {
//                                 let query = jQuery(`#${blockProperties.clientId}`);
//                                 let currentFrame = wp.media({
//                                     title: 'Select Media',
//                                     multiple: false,
//                                     library: {
//                                         type: 'image',
//                                     }
//                                 });

//                                 currentFrame.on('close', function () {
//                                     // On close, get selections and save to the hidden input
//                                     // plus other AJAX stuff to refresh the image preview
//                                     let selection = currentFrame.state().get('selection');

//                                     console.log(selection);
//                                     console.log(blockProperties);
//                                     console.log(query);
//                                 });

//                                 currentFrame.open();
//                             }
//                         }))
//                 }
//             },
//             ),

//         ), BlockEdit(blockProperties))
//         // wp.element.createElement(
//         //     InspectorControls,
//         //     {

//         //     },
//         //     wp.element.createElement(PanelBody, {
//         //         title: 'Panel Title',
//         //         initialOpen: true
//         //     })
//         // )
//     }
// }, 'addMedialibraryControls');

// wp.hooks.addFilter('editor.BlockEdit', 'my-plugin/my-control', addMedialibraryControls);