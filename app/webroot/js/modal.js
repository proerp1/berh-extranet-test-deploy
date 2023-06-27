// JavaScript Document

function verConfirm(locate, str = 'Deseja excluir o registro?'){

	string =  '<div id="myModal" class="modal fade in" >'+
							'<div class="modal-dialog">'+
						    	'<div class="modal-content">'+
										'<div class="modal-header bg-danger">'+
											'<h3 id="myModalLabel" class="text-white"><i class="fa fa-trash-o fa-3"></i> '+str+'</h3>'+
											'<button type="button" class="close btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>'+
										'</div>'+
										'<div class="modal-body">'+
											'Importante: Esta ação não poderá ser desfeita.'+
										'</div>'+
										'<div class="modal-footer">'+
											'<button class="btn" data-bs-dismiss="modal" aria-hidden="true">N&atilde;o</button>'+
											'<button class="btn btn-danger" onClick="javascript: window.location ='+ " '" + locate +"' "+'">Sim</button>'+
										'</div>'+
									'</div>'+
								'</div>'+
						'</div>';

	$(string).modal("show");


}
