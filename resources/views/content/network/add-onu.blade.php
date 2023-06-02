@extends('layouts/layoutMaster')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Network/</span> Add ONU</h4>

<!-- Basic Layout -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{route('network-store-onu')}}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="name">ONU Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="ONU Name" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="mac">MAC Address</label>
                <input type="text" class="form-control" id="mac" name="mac" placeholder="EX: 00:11:22:33:44:55" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="olt_id">OLT</label>
                <select id="olt_id" name="olt_id" class="select2 form-select" onchange="togglePortAndZoneField()">
                    <option value="">Please Select One</option>
                    @foreach($olts as $olt)
                        <option value="{{$olt->id}}">{{$olt->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="pon_port">Pon Port</label>
                <select id="pon_port" name="pon_port" class="select2 form-select">
                    <option value="">Please Select One</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="onu_id">Onu Id</label>
                <input type="text" class="form-control" id="onu_id" name="onu_id" placeholder="Onu Id" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="rx_power">Rx Power</label>
                <input type="text" class="form-control" id="rx_power" name="rx_power" placeholder="Rx Power" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="distance">Distance (M)</label>
                <input type="text" class="form-control" id="distance" name="distance" placeholder="Rx Power" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="user_id">Customer</label>
                <select id="user_id" name="user_id" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3" id="zone_id_field" style="display:none">
                <label class="form-label" for="zone_id">Zone</label>
                <input type="text" class="form-control" id="zone_id" name="zone_id" />
            </div>
            <div class="mb-3 row">
                <div class="col-3">
                    <input type="checkbox" name="vlan_tagged" id="vlan_tagged" onchange="toggleVlanIdField()">
                    <label for="vlan_tagged">VLAN Tagged</label>
                </div>
                <div class="col-9" id="vlan_id_field" style="display:none">
                    <label for="vlan_id" class="col-2 col-form-label">VLAN Id</label>
                    <div class="col-10">
                      <input class="form-control" type="text" id="vlan_id" name="vlan_id"/>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js" integrity="sha512-uMtXmF28A2Ab/JJO2t/vYhlaa/3ahUOgj1Zf27M5rOo8/+fcTUVH0/E0ll68njmjrLqOBjXM3V9NiPFL5ywWPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    function togglePortAndZoneField(){
        let olt_id = document.getElementById('olt_id').value;
        axios.get(`olt-details/${olt_id}`).then((resp)=>{
            if(resp.status == 200){
                let pon_port = resp.data.olt.non_of_pon_port;
                let pon_port_select_field = document.getElementById('pon_port');
                let option = "";
                for(let i=1; i<=pon_port; i++){
                    option = option.concat(`<option value=${i}> ${i} </option>`)
                }

                pon_port_select_field.innerHTML = option;

                let zone_field = document.getElementById('zone_id');
                zone_field.value = resp.data.zone
                document.getElementById('zone_id_field').style.display = 'block';
            }
        })
    }

    function toggleVlanIdField(){
        let vlan_tagged = document.getElementById('vlan_tagged');
        if(vlan_tagged.checked == true){
            let vlan_id_field = document.getElementById('vlan_id_field');
            vlan_id_field.style.display = 'block'
        }
        else{
            let vlan_id_field = document.getElementById('vlan_id_field');
            vlan_id_field.style.display = 'none'
        }
    }
</script>