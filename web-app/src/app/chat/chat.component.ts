import { Component, OnInit } from '@angular/core';
import { ChatComponentService } from "./chat.component.service";
// import { UsersComponentService } from "../users/users.component.service";
import {UsersComponentService} from "../dashboard/cards/dashboard-users/dashboard-users.component.service";

@Component({
  selector: 'app-chat',
  templateUrl: './chat.component.html',
  styleUrls: ['./chat.component.scss'],
  providers: [ChatComponentService,
  UsersComponentService],

})
export class ChatComponent implements OnInit {
  friends: any;
  chats = [];
  chatSelected: number;
  messages: any;
  partner: any;

  constructor(private chatService: ChatComponentService, private userService: UsersComponentService) { }

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
      // this.messages=data.messagesIndexed.data;
      // alert(data.messagesIndexed.partner[0].name);
      // alert(JSON.stringify(this.messages))
    });
  }

  allAction() {
      this.chatService.allAction().subscribe(data => {

      });
    }

  newAction(id) {
    this.chatService.newAction(id).subscribe(data => {
      // this.idAction();
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

  // idAction() {
  //   this.userService.idAction().subscribe(data => {
  //     alert(data.id)
  //     return data
  //   })
  // }
}