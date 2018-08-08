<div class="col-sm-12">
    <div class="form-group">
        <label for="exampleInputEmail1">Driver Name</label>
        <input type="text" class="form-control" name="full_name"
               value="{{ ($driver && $driver->userR) ? $driver->userR->full_name : '' }}" placeholder="Enter Driver Name">
    </div>

    <div class="form-group">
        <label for="exampleInputEmail1">Company Name (Optional)</label>
        <input type="text" class="form-control" name="company_name"
               value="{{ $driver ? $driver->company_name : '' }}" placeholder="Enter Company Name">
    </div>

    <div class="form-group">
        <label for="exampleInputEmail1">Driver ID</label>
        <div class="status_red">
            {{ $driver->id ? $driver->driver_code : $driver_code }}
            <input type="hidden" name="driver_code" value="{{ $driver->id ? $driver->driver_code : $driver_code }}" />
        </div>
    </div>

    <div class="form-group">
        <label for="exampleInputEmail1">Email ID</label>
        <input type="email" class="form-control" name="email"
               value="{{ ($driver && $driver->userR) ? $driver->userR->email : '' }}" placeholder="Enter Email ID"
               required="true" autocomplete="off" <?= $driver->id ? 'disabled="true"' : '' ?>>
    </div>

    <div class="form-group">
        <label for="exampleInputEmail1">Password</label>
        <input type="password" class="form-control" name="password"
               value="{{ ($driver && $driver->userR) ? $driver->userR->password : '' }}" placeholder="Enter Password"
               required="true" autocomplete="off" <?= $driver->id ? 'disabled="true"' : '' ?>>
    </div>

    <div class="sub-title">Type Of Services</div>
    <div>
        <div class="checkbox3 checkbox-inline checkbox-check checkbox-light">
            <input type="checkbox" name="vehicle_type[]" id="flat-bed" value="{{ VEHICLE_TYPE_FLAT_BED }}"
                {{ ($driver && substr_count($driver, VEHICLE_TYPE_FLAT_BED)) ? 'checked' : '' }} >
            <label for="flat-bed">
                Flat Bed
            </label>
        </div>
        <div class="checkbox3 checkbox-inline checkbox-check checkbox-light">
            <input type="checkbox" name="vehicle_type[]" id="normal" value="{{ VEHICLE_TYPE_NORMAL }}"
                {{ ($driver && substr_count($driver, VEHICLE_TYPE_NORMAL)) ? 'checked' : '' }} >
            <label for="normal">
                Normal
            </label>
        </div>
    </div>
    <br/>
    <div class="form-group">
        <label for="exampleInputEmail1">Vehicle Number</label>
        <input type="text" class="form-control" name="vehicle_number"
               value="{{ $driver ? $driver->vehicle_number : '' }}" placeholder="Enter Vehicle Number">
    </div>

    <div class="gallery">
        <div class="form-group">
            <label for="">Upload Driver Photo</label>
            <div class="clearfix"></div>
            <div class="b_l_gal" style="width: 100px; height: 70px">
                @if($driver->userR && $driver->userR->avatar)
                    <img src="{{ asset('upload/account/'.$driver->userR->avatar) }}" class="img-responsive"/>
                @endif
            </div>
            <div class="clearfix"></div>
            <input type="file" name="avatar" <?= $driver->id ? '' : 'required="true"' ?>>
        </div>
    </div> <!-- gallery-->

    <div class="gallery">
        <div class="form-group">
            <label for="">Upload Driver License</label>
            <div class="clearfix"></div>
            <div class="b_l_gal" style="width: 100px; height: 70px">
                @if($driver->driver_license)
                    <img src="{{ asset('upload/driver/'.$driver->driver_license) }}" class="img-responsive"/>
                @endif
            </div>
            <div class="clearfix"></div>
            <input type="file" name="driver_license" <?= $driver->id ? '' : 'required="true"' ?>>
        </div>
    </div> <!-- gallery-->

    <div class="gallery">
        <div class="form-group">
            <label for="">Upload Driver Emirates ID</label>
            <div class="clearfix"></div>
            <div class="b_l_gal" style="width: 100px; height: 70px">
                @if($driver->emirate_id)
                    <img src="{{ asset('upload/driver/'.$driver->emirate_id) }}" class="img-responsive"/>
                @endif
            </div>
            <div class="clearfix"></div>
            <input type="file" name="emirate_id">
        </div>
    </div> <!-- gallery-->

    <div class="gallery">
        <div class="form-group">
            <label for="">Upload Mulkiya</label>
            <div class="clearfix"></div>
            <div class="b_l_gal" style="width: 100px; height: 70px">
                @if($driver->mulkiya)
                    <img src="{{ asset('upload/driver/'.$driver->mulkiya) }}" class="img-responsive"/>
                @endif
            </div>
            <div class="clearfix"></div>
            <input type="file" name="mulkiya">
        </div>
    </div> <!-- gallery-->
</div>