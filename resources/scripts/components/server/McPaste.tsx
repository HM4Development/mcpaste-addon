import React, { useEffect, useState } from 'react';
import tw from "twin.macro";
import Button from "@/components/elements/Button";
import { ServerContext } from "@/state/server";
import { SocketEvent } from "@/components/server/events";
import Fade from "@/components/elements/Fade";
import { SwitchTransition } from "react-transition-group";
import styled, { keyframes } from "styled-components/macro";
import stripAnsi from 'strip-ansi';
import shareServerLog, { PasteResponse } from '../../api/server/shareServerLog';
import { IconProp, library } from '@fortawesome/fontawesome-svg-core';
import * as Icons from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

Object.keys(Icons)
    .filter(it => it !== 'key' && it !== 'prefix')
    // @ts-ignore
    .forEach(it => library.add(Icons[it]))

interface Props {
    position: 'component' | 'commandLine'
}

interface MCPasteData {
    tokenValid: boolean,
    style: MCPasteStyle
}

interface MCPasteStyle {
    buttonLocation: 'component' | 'commandLine'
    textButtonText: string
    textColor: string
    textButtonColor: string
    textButtonHoverColor: string
    boxColor: string
    textSize: 'text-xs' | 'text-sm' | 'text-base' | 'text-lg' | 'text-xl'
    buttonSize: 'xsmall' | 'small' | 'large' | 'xlarge'
    shadow: boolean
    icon: string
    iconColor: string
    iconHoverColor: string
    toastTextColor: string
    toastBoxColor: string
    toastBorderColor: string
    toastOpacity: number
    toastText: string
    toastErrorText: string
}

// @ts-ignore
export const mcPasteData: MCPasteData = window.MCPasteData
export const mcPasteStyle: MCPasteStyle = mcPasteData.style

const fade = keyframes`
    from { opacity: 0 }
    to { opacity: 1 }
`;

const Toast = styled.div`
    ${tw`fixed z-50 bottom-0 left-0 mb-4 w-full flex justify-end pr-4`};
    animation: ${fade} 250ms linear;

    & > div {
        ${tw`rounded px-4 py-2 border`};
        background-color: ${mcPasteStyle.toastBoxColor};
        border-color: ${mcPasteStyle.toastBorderColor};
        opacity: ${mcPasteStyle.toastOpacity / 100};
    }

    & p {
        color: ${mcPasteStyle.toastTextColor};
    }
`;

// I took the lazy approach when doing these 2 elements, it was 3:11 am when I was doing this so.....
const CMDButton = styled.div`
    ${tw`p-1 transition-all rounded-br cursor-pointer`}

    &:hover {
        background-color: ${mcPasteStyle.iconHoverColor};
    }
`

const CMDButtonDisabled = styled.div`
    ${tw`p-1 transition-all rounded-br`}
`

// fuck tailwind
const textSizeMap = {
    'text-xs': tw`text-xs`,
    'text-sm': tw`text-sm`,
    'text-base': tw`text-base`,
    'text-lg': tw`text-lg`,
    'text-xl': tw`text-xl`,
}

const ComponentButton = styled(Button)`
    ${tw`transition-all`};
    background-color: ${mcPasteStyle.textButtonColor};

    &:hover {
        background-color: ${mcPasteStyle.textButtonHoverColor};
    }
`

export default ({ position }: Props) => {
    const [ log, setLog ] = useState<string[]>([]);
    const addLog = (data: string) => setLog(prevLog => [...prevLog, data.startsWith(">") ? data.substring(1) : data])

    const [ uploading, setUploading ] = useState(false)
    const [ copied, setCopied ] = useState<false | string | { error: string }>(false)
    const { connected, instance } = ServerContext.useStoreState(state => state.socket);

    const uuid = ServerContext.useStoreState(state => state.server.data!.uuid)

    useEffect(() => {
        if (!connected || !instance) return

        instance.addListener(SocketEvent.CONSOLE_OUTPUT, addLog);

        return () => {
            instance.removeListener(SocketEvent.CONSOLE_OUTPUT, addLog);
        }
    }, [ connected, instance ])

    const [ toastText, setToastText ] = useState("")

    useEffect(() => {
        if (!copied) return
        if (typeof copied === "string") {
            setToastText(mcPasteStyle.toastText.replace("%key%", copied));
            return;
        }
        setToastText(mcPasteStyle.toastErrorText.replace("%error%", copied.error))
    }, [ copied ])

    const resetStateAfter = (ms = 2500) => {
        setTimeout(() => {
            setCopied(false)
            setUploading(false);
        }, ms);
    }

    const mcPaste = () => {
        if (uploading) return
        const data = stripAnsi(log.map(it => it.replace("\r", "")).join("\n")) || "";
        setUploading(true);
        shareServerLog(uuid, data)
            .then((data: PasteResponse): string => {
                if (navigator.clipboard !== undefined) {
                    navigator.clipboard.writeText(`https://mcpaste.com/${data.key}`);
                    return data.key
                }

                const area = document.createElement("textarea");
                area.value = `https://mcpaste.com/${data.key}`;
                area.style.position = "fixed";
                document.body.appendChild(area);
                area.focus();
                area.select();
                area.setSelectionRange(0, 99999);

                document.execCommand("copy");

                document.body.removeChild(area);
                return data.key;
            }).then((key) => {
                setCopied(key);
                resetStateAfter();
            })
            .catch(() => {
                setCopied({ error: "Bitch, waht the fawk!!" })
                resetStateAfter()
            })
    }

    const CMDButtonType = uploading ? CMDButtonDisabled : CMDButton

    const content = position === 'component' ?
        (
            <div css={[tw`rounded p-3 flex mt-4 justify-center`, mcPasteStyle.shadow ? tw`shadow-md` : '']} style={{ backgroundColor: mcPasteStyle.boxColor }}>
                <ComponentButton
                    size={mcPasteStyle.buttonSize}
                    isSecondary
                    css={tw`mr-2`}
                    disabled={uploading}
                    onClick={e => {
                        mcPaste()
                    }}
                >
                    <div css={textSizeMap[mcPasteStyle.textSize]} style={{ color: mcPasteStyle.textColor }}>
                        { mcPasteStyle.textButtonText }
                    </div>
                </ComponentButton>
            </div>
        )
        :
        (
            <CMDButtonType onClick={e => {
                mcPaste()
            }}>
                <div css={[tw`flex-shrink-0 p-2 font-bold`, uploading ? '' : tw`cursor-pointer`]}>
                    <FontAwesomeIcon icon={mcPasteStyle.icon as IconProp} fixedWidth size={'lg'} color={mcPasteStyle.iconColor} />
                </div>
            </CMDButtonType>
        )

    return (
        <div>
            { content }
            <SwitchTransition>
                <Fade timeout={250} key={copied !== false ? 'visible' : 'invisible'}>
                    {copied !== false ?
                        <Toast>
                            <div>
                                <p css={tw`text-lg`}>{toastText}</p>
                            </div>
                        </Toast>
                        : <></>
                    }
                </Fade>
            </SwitchTransition>
        </div>
    )
}
