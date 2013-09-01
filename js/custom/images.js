Ext.Loader.setConfig({
    enabled: true
});

Ext.require([
    'Ext.form.field.File',
    'Ext.form.field.Number',
    'Ext.form.Panel',
    'Ext.window.MessageBox',
    'Ext.grid.*',
    'Ext.data.*'
]);

Ext.onReady(function() {
    var win;
    var linkWin;
    var msg = function(title, msg) {
        Ext.Msg.show({
            title: title,
            msg: msg,
            minWidth: 200,
            modal: true,
            icon: Ext.Msg.INFO,
            buttons: Ext.Msg.OK
        });
    };
    var states = Ext.create('Ext.data.Store', {
        fields: ['abbr', 'name'],
        data : [
            {"val":"1", "name":"1"},
            {"val":"2", "name":"2"},
            {"val":"3", "name":"3"},
            {"val":"4", "name":"4"},
            {"val":"5", "name":"5"},
            {"val":"6", "name":"6"},
            {"val":"7", "name":"7"},
            {"val":"8", "name":"8"}

        ]
    });
    Ext.apply(Ext.form.VTypes, {
        fileUpload: function(val, field) {
            var fileName = /^.*\.(gif|png|jpg|jpeg)$/i;
            return fileName.test(val);
        },
        fileUploadText: 'Image must be in .gif,.png,.jpg,.jpeg format'
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        renderTo: 'fileupload',
        width: 500,
        frame: true,
        title: 'Image upload form',
        bodyPadding: '10 10 0',
        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 70
        },

        items: [
            {
                xtype: 'filefield',
                fieldLabel: 'Photo',
                name: 'photo-path[]',
                buttonText: 'Browse',
                vtype:'fileUpload'
            }
        ],
        buttons: [{
            text: 'Save',
            handler: function(){
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        url: 'home/imageUpload',
                        waitMsg: 'Uploading your photos...',
                        success: function(fp, o) {
                            msg('Success', 'Files loaded: ' + o.result.count);
                            Ext.getCmp('imageGrid').getStore().load();
                        },
                        failure: function() {
                            Ext.Msg.alert("Error", Ext.JSON.decode(this.response.responseText).message);
                        }
                    });
                }
            }
        },{
            text: 'Reset',
            handler: function() {
                this.up('form').getForm().reset();
            }
        }]
    });
    Ext.create('Ext.form.ComboBox', {
        fieldLabel: 'Choose files count',
        store: states,
        queryMode: 'local',
        displayField: 'name',
        valueField: 'val',
        value: 1,
        renderTo: 'filecount',
        allowBlank: false,
        listConfig: {
            listeners: {
                itemclick: function(list, record) {
                    var count = record.get('name');
                    formPanel.removeAll();
                    for(var i = 0; i < count; i++){
                        formPanel.add(new Ext.form.field.File({
                            xtype: 'filefield',
                            fieldLabel: 'Photo',
                            buttonText: 'Browse',
                            name:'photo-path[]',
                            vtype:'fileUpload'
                        }));
                    }
                }
            }
        }
    });

    //grid

    Ext.define('ImageStore',{
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id'},
            {name: 'imagename'},
            {name: 'imagetype'},
            {name: 'date'},
            {name: 'fullpath'}
        ]
    });
    var imageStore = Ext.create('Ext.data.JsonStore', {
        model: 'ImageStore',
        pageSize: 8,
        proxy: {
            type: 'ajax',
            url: '/home/getImages',
            reader: {
                type: 'json',
                root: 'images',
                totalProperty: 'totalCount'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            }
        }
    });
    imageStore.load();
    var listView = Ext.create('Ext.grid.Panel', {
        width:600,
        height:250,
        collapsible:true,
        title:'Your images <i>(0 items selected)</i>',
        renderTo: 'imageGrid',
        id: 'imageGrid',
        store: imageStore,
        multiSelect: true,
        viewConfig: {
            emptyText: 'No images to display'
        },
        tbar: [{
            text: 'Remove',
            id: 'removeImageButton',
            handler : function() {
                var selectedRows = listView.getSelectionModel().getSelection();
                var selectedIds = [];
                for(var i = 0; i < listView.getSelectionModel().getCount(); i++){
                    selectedIds.push(selectedRows[i].internalId)
                }
                var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Please wait..."});
                Ext.Ajax.request({
                    url: 'home/deleteImages',    // where you wanna post
                    method: 'POST',
                    params: {
                        images: Ext.encode(selectedIds)
                    },
                    success: function(){ // function called on success
                        Ext.getCmp('imageGrid').getStore().load();
                        myMask.destroy();
                        msg('Success','Success');

                    },
                    failure: function(){
                        Ext.getCmp('imageGrid').getStore().load();
                        myMask.destroy();
                        msg('Fail','Fail');
                    }
                });
            },
            disabled: true
        },{
            text: 'Get link to control access',
            id: 'getImageLink',
            disabled: true,
            handler: function(){
                if (!linkWin) {
                    linkWin = Ext.create('widget.window', {
                        title: 'Link',
                        closable: true,
                        width: 500,
                        height: 100,
                        resizable: false,
                        layout: {
                            type: 'border',
                            padding: 5
                        },
                        items: [
                            {
                                xtype: 'textfield',
                                id: 'linkToImage',
                                readOnly: true,
                                width: 500
                            }
                        ]
                    });
                }
                if(!linkWin.isVisible()){
                    linkWin.show(this,function(){
                        Ext.getCmp('linkToImage').setValue(window.location.origin+'/'+'home/imageAccess?id='+listView.getSelectionModel().getSelection()[0].raw.id);

                    });
                }
            }
        }],
        columns: [{
            text: 'Id',
            flex: 15,
            dataIndex: 'id'
        },{
            text: 'Name',
            flex: 30,
            dataIndex:'imagename'
        },{
            text: 'Type',
            flex: 25,
            dataIndex: 'imagetype'
        },{
            text: 'Date',
            flex: 20,
            dataIndex: 'date'
        },{
            text: 'Full path',
            flex: 60,
            dataIndex: 'fullpath'
        }],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: imageStore,
            displayInfo: true,
            displayMsg: '{0} - {1} of {2}',
            emptyMsg: "No images to display" }
        ),
        listeners : {
            itemdblclick: function(dv, record, item, index, e) {

                if(!win) {
                    win = Ext.create('widget.window', {
                        title: 'Image',
                        header: {
                            titlePosition: 2,
                            titleAlign: 'center'
                        },
                        closable: true,
                        closeAction: 'hide',
                        width: 600,
                        minWidth: 350,
                        height: 350,
                        tools: [{type: 'pin'}],
                        layout: {
                            type: 'border',
                            padding: 5
                        },
                        autoScroll: true,
                        items :[
                            {
                                xtype: 'container',
                                items: [{
                                    xtype: 'image',
                                    id: 'bigImage',
                                    autoScroll: true
                                }]
                            }
                        ]
                    });
                }
                if(!win.isVisible()) {
                    win.show(this, function() {
                        Ext.getCmp('bigImage').setSrc(window.location.origin+'/'+record.raw.fullpath);
                    });
                }
            }
        }
    });
    listView.on('selectionchange', function(view, nodes){
        var len = nodes.length,
            suffix = len === 1 ? '' : 's',
            str = 'Your images <i>({0} item{1} selected)</i>';
        if(len > 0){
            Ext.getCmp('removeImageButton').enable();
        } else {
            Ext.getCmp('removeImageButton').disable();
        }

        if(len == 1) {
            Ext.getCmp('getImageLink').enable();
        } else {
            Ext.getCmp('getImageLink').disable();
        }
        listView.setTitle(Ext.String.format(str, len, suffix));
    });

});
