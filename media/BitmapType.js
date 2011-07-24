var BitmapType = function() {
	
	return {
	    
		// Insert a canvas element in place of text.

		convert: function(el, attr) {
			var text = el.innerHTML;
			el.innerHTML = '';

			var canvas = document.createElement('canvas');
			canvas.id = el.id + '-bitmap-type-canvas';
			el.appendChild(canvas);

			var tokens = BitmapType.tokenize(text);

			var scale = attr.scale || 1;
                        var lineSpacing = attr.lineSpacing || 1;
			var font = attr.font || Fonts.Silkscreen;

			var length = BitmapType.calculateLength(tokens, font, scale, lineSpacing);
			var width = attr.width || length.totalWidth;
			var height = length.lineHeight * Math.ceil(length.totalWidth / width);

			for(var index = 0, position = 0, rows = 1, tLen = tokens.length; index < tLen; index++) {
				var token = tokens[index],
				    glyph = font[token];
			
				for(var n = index, len = tokens.length; n < len; n++) {
					if(tokens[n] === 'space') break;
				}
				var nextSpace = n;
			        var wordWidth = BitmapType.calculateLength(tokens.slice(index, nextSpace), font, scale).totalWidth;
			        var letterWidth = BitmapType.calculateLength(tokens.slice(index, index + 1), font, scale).totalWidth;
			
				if((position + wordWidth > width) && ((tokens[index - 1] === 'space' && wordWidth <= width) || (position + letterWidth > width))) {
					rows++;
					position = 0;
				}
				position += (glyph[0].length + 1) * scale;							
			}
			height = length.lineHeight * rows;

			var angle = attr.angle || 0;
			var canvasWidth = (angle === 90 || angle === 270) ? height : width;
			var canvasHeight = (angle === 90 || angle === 270) ? width : height;
			
			if(YAHOO.env.ua.webkit >= 412 && YAHOO.env.ua.webkit < 522) { // Safari 2
				canvas.style.width = canvasWidth + 'px';
				canvas.style.height = canvasHeight + 'px';
			}
			else {				
				canvas.width = canvasWidth;
				canvas.height = canvasHeight;
			}

			if(typeof G_vmlCanvasManager !== 'undefined') { // IE only.
				G_vmlCanvasManager.initElement(canvas); // Apply the behavior with excanvas.js.
				canvas = YAHOO.util.Dom.get(el.id + '-bitmap-type-canvas'); // Get a new reference to the canvas.
			}
			var ctx = canvas.getContext('2d');
			
			// Get the current color of the text.
			
			var color = '#000'; // Default color.
			if(el.currentStyle) { // IE
				color = el.currentStyle['color'];
			}
			else if(window.getComputedStyle) {
				var style = window.getComputedStyle(el, '');
				color = style.getPropertyValue('color');
			}
			ctx.fillStyle = color;

			var horizPosition = 0, row = 0;
			for(var n = 0, len = tokens.length; n < len; n++) {
				var position = BitmapType.translate(tokens, n, horizPosition, row, width, length.lineHeight, ctx, font, angle, scale, width);
				horizPosition = position[0];
				row = position[1];
			}
		},
		
		// Draw text on a canvas element.
		
		addStringToCanvas: function(text, ctx, attr) {
			var x = attr.x || 0;
			var y = attr.y || 0;
			var font = attr.font || Fonts.Silkscreen;
			var scale = attr.scale || 1;
                        var lineSpacing = attr.lineSpacing || 1;
			var angle = attr.angle || 0;
			var color = attr.color || '#000';
			var textAlign = attr.textAlign || 'left';
			
			var tokens = BitmapType.tokenize(text);

			var length = BitmapType.calculateLength(tokens, font, scale, lineSpacing);
			var width = attr.width || length.totalWidth;
			var height = length.lineHeight * Math.ceil(length.totalWidth / width);

			for(var index = 0, position = 0, rows = 1, tLen = tokens.length; index < tLen; index++) {
				var token = tokens[index],
				    glyph = font[token];
			
				for(var n = index, len = tokens.length; n < len; n++) {
					if(tokens[n] === 'space') break;
				}
				var nextSpace = n;
			        var wordWidth = BitmapType.calculateLength(tokens.slice(index, nextSpace), font, scale).totalWidth;
			        var letterWidth = BitmapType.calculateLength(tokens.slice(index, index + 1), font, scale).totalWidth;
			
				if((position + wordWidth > width) && ((tokens[index - 1] === 'space' && wordWidth <= width) || (position + letterWidth > width))) {
					rows++;
					position = 0;
				}
				position += (glyph[0].length + 1) * scale;							
			}
			height = length.lineHeight * rows;
			ctx.fillStyle = color;

			var horizPosition = 0, row = 0;
			if(textAlign === 'right' && height === length.lineHeight && length.totalWidth < width) {
			        horizPosition = width - length.totalWidth;
			}
			for(var n = 0, len = tokens.length; n < len; n++) {
				var position = BitmapType.translate(tokens, n, horizPosition, row, width, length.lineHeight, ctx, font, angle, scale, width, x, y);
				horizPosition = position[0];
				row = position[1];
			}
		},
		
		// Helper methods.
		
		tokenize: function(text) {
			if(text === null || text === undefined) text = '';
			text = text.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');

			for(var n = 0, len = text.length, tokens = [], token, letter; n < len; n++, token = '') {
				letter = text.charAt(n);
				if(letter.match(/[a-zA-Z]/)) {
					token = letter;
				}
				else switch(letter) {
					case ' ':  token = 'space';             break;

					case '1':  token = 'one';               break;          case '2':  token = 'two';               break;
					case '3':  token = 'three';             break;          case '4':  token = 'four';              break;
					case '5':  token = 'five';              break;          case '6':  token = 'six';               break;
					case '7':  token = 'seven';             break;          case '8':  token = 'eight';             break;
					case '9':  token = 'nine';              break;          case '0':  token = 'zero';              break;

					case '!':  token = 'exclaimationPoint'; break;          case '@':  token = 'at';                break;
					case '#':  token = 'hash';              break;          case '$':  token = 'dollar';            break;
					case '%':  token = 'percentage';        break;          case '^':  token = 'caret';             break;
					case '&':  token = 'ampersand';         break;          case '*':  token = 'star';              break;
					case '(':  token = 'leftParen';         break;          case ')':  token = 'rightParen';        break;
					case '_':  token = 'underscore';        break;          case '-':  token = 'dash';              break;
					case '+':  token = 'plus';              break;          case '=':  token = 'equals';            break;
					case '`':  token = 'backtick';          break;          case '~':  token = 'tilde';             break;
					case '[':  token = 'leftBracket';       break;          case ']':  token = 'rightBracket';      break;
					case '\\': token = 'leftSlash';         break;          case '{':  token = 'leftCurlyBracket';  break;
					case '}':  token = 'rightCurlyBracket'; break;          case '|':  token = 'pipe';              break;
					case ';':  token = 'semiColon';         break;          case ':':  token = 'colon';             break;
					case '\'': token = 'singleQuote';       break;          case '"':  token = 'doubleQuote';       break;
					case ',':  token = 'comma';             break;          case '.':  token = 'period';            break;
					case '/':  token = 'rightSlash';        break;          case '<':  token = 'leftAngleBracket';  break;
					case '>':  token = 'rightAngleBracket'; break;          case '?':  token = 'questionMark';      break;
					default:   token = ''; 
				}
				if(token !== '') tokens.push(token);
			}						
			return tokens;
		},
		
		calculateLength: function(tokens, font, scale, lineSpacing) {
			var length = { totalWidth: 0 };
			for(var n = 0, len = tokens.length; n < len; n++) {
				length.totalWidth += font[tokens[n]][0].length + scale;
			}
			length.totalWidth = length.totalWidth * scale;
			length.lineHeight = (font['space'].length + lineSpacing) * scale;
			return length;
		},
		
		translate: function(tokens, index, position, row, width, height, ctx, font, angle, scale, length, x, y) {
                        if(x === undefined) x = 0;
                        if(y === undefined) y = 0;        

			var token = tokens[index],
			    glyph = font[token];
			
			for(var n = index, len = tokens.length; n < len; n++) {
				if(tokens[n] === 'space') break;
			}
			var nextSpace = n;
			var wordWidth = BitmapType.calculateLength(tokens.slice(index, nextSpace), font, scale).totalWidth;
			var letterWidth = BitmapType.calculateLength(tokens.slice(index, index + 1), font, scale).totalWidth;
			
			if((position + wordWidth > width) && ((tokens[index - 1] === 'space' && wordWidth <= width) || (position + letterWidth > width))) {
                                row++;
			    position = 0;
			}
			for(var n = 0, len = glyph.length * scale; n < len; n += scale) {
				for(var m = 0, mLen = glyph[n / scale].length * scale; m < mLen; m += scale) {
					if(!glyph[n / scale][m / scale]) continue;
					switch(angle) {
						case 90:  ctx.fillRect(len - n + x, m + position + y, scale, scale);                    break;
						case 180: ctx.fillRect(length - (m + position) - 1 + x, len - n + y, scale, scale);     break;
						case 270: ctx.fillRect(n + x, length - (m + position) - 1 + y, scale, scale);           break;		
						default:  ctx.fillRect(m + position + x, n + (row * height) + y, scale, scale);         break;
					}
				}
			}
			return [position + mLen + scale, row];
		}
	};
}();