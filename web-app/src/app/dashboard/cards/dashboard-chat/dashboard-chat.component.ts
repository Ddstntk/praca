import {Component, Injector, OnInit} from '@angular/core';
import {DashboardCard} from '../dashboard-card';
import {AbstractDashboardCard} from '../abstract-dashboard-card';
import {ChatComponentService} from "../../../chat/chat.component.service";
import {UsersComponentService} from "../../../users/users.component.service";



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

  ngOnInit() {
    // this.friends = this.friendsService.indexAction();
    // alert(JSON.stringify(this.friends));
    this.chatSelected=81;
    this.chatService.allAction().subscribe(data => {
      this.chats = data.chatsIndexed[0];
      this.indexAction(this.chatSelected);
    });
  }

  indexAction(chat) {
    this.chatService.indexAction(chat).subscribe(data => {
      this.messages=data.messagesIndexed.data;
      // alert(JSON.stringify(this.messages))
    });
  }

  allAction() {
    this.chatService.allAction().subscribe(data => {

    });
  }

  newAction(id) {
    this.chatService.newAction(id).subscribe(data => {
      this.idAction();
    });
  }

  sendAction(messageBody) {
    this.chatService.sendAction({body:messageBody, id:this.chatSelected}).subscribe(data => {
      this.indexAction(this.chatSelected);
    });
  }

  selectChat(value) {
    this.chatSelected = value;
  }

  idAction() {
    this.userService.idAction().subscribe(data => {
      alert(data.id)
      return data
    })
  }
}