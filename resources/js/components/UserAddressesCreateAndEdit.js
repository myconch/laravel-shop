//创建一个新的Vue组件来监听select-district的change事件，方便将省市区组件的数据放到表单里提交到后端

//注册一个名为user-addresses-create-and-edit的组件
Vue.component('user-addresses-create-and-edit',{
    //组件数据
    data(){
        return {
            province: '',
            city: '',
            district : '',
        }
    },
    methods:{

        //把参数val中的值保存到组件的数据中
        onDistrictChanged(val) {
            console.log(val);
            if (val.length === 3){
                this.province = val[0];
                this.city = val[1];
                this.district = val[2];
            }
        }
    }
});