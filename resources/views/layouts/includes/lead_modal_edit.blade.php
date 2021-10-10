<div class="modal" id="modalLeadEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Edit</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="name" class="control-label">Name</label>
                        <input name="name" class="form-control name" id="editFields" />
                    </div>
                    <div class="form-group">
                        <label for="phone" class="control-label">Phone</label>
                        <input name="phone" class="form-control phone" id="editFields" />
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input name="email" class="form-control email" id="editFields" />
                    </div>
                    <div class="form-group">
                        <label for="address" class="control-label">Address</label>
                        <textarea name="address" class="form-control address" id="editFields"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="quantity" class="control-label">Quantity</label>
                        <input name="quantity" class="form-control quantity" id="editFields"></input>
                    </div>
                    
                    <div class="form-group">
                        <label for="price" class="control-label">Price</label>
                        <input name="price" class="form-control price" id="editFields"></input>
                    </div>
                    <div class="form-group">
                        <label for="note" class="control-label">Note</label>
                        <textarea name="note" class="form-control note" id="editFields"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="caller_status" class="control-label">Caller Status</label>
                        <select name="caller_status" id="editFields" class="form-control callerstatus">
                            @foreach (App\Models\Lead::statuses as $status)
                                <option value="{{ $status }}"> {{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary saveEditButton">Save</button>
            </div>
        </div>
    </div>
</div>
