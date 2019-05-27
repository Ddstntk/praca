import {Component, Injector, OnInit} from '@angular/core';
import {DashboardCard} from '../dashboard-card';
import {AbstractDashboardCard} from '../abstract-dashboard-card';
import {ChatComponentService} from "../../../chat/chat.component.service";
// import {UsersComponentService} from "../../../users/users.component.service";
import {UsersComponentService} from "../dashboard-users/dashboard-users.component.service";


@Component({
  selector: 'app-dashboard-chat',
  templateUrl: './dashboard-chat.component.html',
  styleUrls: ['./dashboard-chat.component.scss']
})
export class DashboardChatComponent extends AbstractDashboardCard implements OnInit {

  constructor(private injector: Injector, private chatService: ChatComponentService, private userService: UsersComponentService) {
    super(injector.get(DashboardCard.metadata.NAME),
      injector.get(DashboardCard.metadata.ROUTERLINK),
      injector.get(DashboardCard.metadata.ICONCLASS),
      injector.get(DashboardCard.metadata.COLS),
      injector.get(DashboardCard.metadata.ROWS),
      injector.get(DashboardCard.metadata.COLOR));
  }
  friends: any;
  chats = [];
  chatSelected: number;
  messages: any;
  userId: any;
  partner: any;
  messageUpdater: any;


  ngOnInit() {
    // this.friends = this.friendsService.indexAction();
    // alert(JSON.stringify(this.friends));
    // this.idAction()
    this.userId = localStorage.getItem("userId")
    this.chatSelected = parseFloat(localStorage.getItem("chatSelected"));
    this.allAction();
    this.messageUpdater = this.listenForMessages();
    // this.chatService.allAction().subscribe(data => {
    //   this.chats = data.chatsIndexed[0];
    //   this.indexAction(this.chatSelected);
    // });
  }

  listenForMessages() {
    return setInterval(()=>{
      this.allAction()
    }, 10000)
  }

  styleObject(message): Object {
    if( message.PK_idUsers === this.userId ) {
      return {float: "right"}
    } else {
      return {float: "left", background: "#337ab7"}
    }
  }

  indexAction(chat) {
    this.chatService.indexAction(chat).subscribe(data => {
      this.messages=data.messagesIndexed.data;
      this.partner = data.messagesIndexed.partner[0].name + " " + data.messagesIndexed.partner[0].surname;
      // alert(JSON.stringify(this.messages))
    });
  }

  allAction() {
    this.chatService.allAction().subscribe(data => {
      this.chats = data.chatsIndexed;
      this.chatSelected = this.chatSelected ? this.chatSelected : this.chats[0][1].FK_idConversations;
      localStorage.setItem("chatSelected", this.chatSelected.toString())

      this.indexAction(this.chatSelected);
    });
  }

  // newAction(id) {
  //   this.chatService.newAction(id).subscribe(data => {
  //     this.idAction();
  //   });
  // }

  sendAction(messageBody) {
    this.chatService.sendAction({body:messageBody, id:this.chatSelected}).subscribe(data => {
      this.indexAction(this.chatSelected);
    });
  }

  selectChat(value) {
    this.chatSelected = value;
    this.allAction();
  }

  // idAction() {
  //   this.userService.getIdAction().subscribe(data => {
  //       localStorage.setItem("userId", data.userId)
  //   })
  // }
}